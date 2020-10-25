<?php

defined('IN_CART') or die;

/**
 *  
 * 友情连接
 *
 *
 * */
class Flink extends Base
{

    /**
     *  
     * 图片广告
     *
     *
     * */
    public function index()
    {
        //广告标签
        $tag = empty($_REQUEST["tag"]) ? "" : trim($_REQUEST["tag"]);
        $adtags = $this->getAdTag();

        //搜索条件
        $where = "isdel = 0";
        $tag && ($where .= " AND tag = '" . $tag . "'");

        $this->data['piclist'] = DB::getDB()->select("adpic", "*", $where, array("tag", "order"));
        $this->data['tagopt'] = array2select($adtags, "val", "val", $tag);
        $this->data['tag'] = $tag;
        $this->output("flink_index");
    }

    /**
     *  
     * 增加图片广告
     *
     *
     * */
    public function picadd()
    {
        $this->data["opertype"] = "add";
        $this->data['tagopt'] = array2select($this->getAdTag(), "val", "val");
        $this->data['leftcur'] = "flink_index";
        $this->output("flink_oper");
    }

    /**
     *  
     * 修改图片广告
     *
     *
     * */
    public function picedit()
    {
        $picid = intval($_GET["picid"]);
        $this->data["opertype"] = "edit";
        $this->data["picid"] = $picid;
        $this->data["pic"] = DB::getDB()->selectrow("adpic", "*", "picid='$picid'");
        $this->data['tagopt'] = array2select($this->getAdTag(), "val", "val", $this->data["pic"]['tag']);
        $this->data["leftcur"] = "flink_index";
        $this->output("flink_oper");
    }

    /**
     *  
     * 修改图片广告
     *
     *
     * */
    public function picsave()
    {
        $text = __("adpic");
        $opertype = strtolower($_POST["opertype"]);
        $ret = false;
        switch ($opertype) {
            case 'add':
            case 'edit':
                $tag = $_POST["tag"];
                $link = $_POST["link"];
                $pic = $_POST["pic"];
                $name = $_POST["name"];
                $data = array("tag" => $tag, "link" => $link, "pic" => $pic, "name" => $name);
                if ($img = getimagesize($pic)) {
                    $data['width'] = $img[0];
                    $data['height'] = $img[1];
                }
                if ($opertype == "add") {
                    $this->adminlog("al_adpic", array("do" => "add", "name" => $name));
                    $ret = DB::getDB()->insert("adpic", $data);
                    $this->setHint(__('add_success', $text), "flink_index");
                } else {
                    $picid = intval($_POST["picid"]);
                    $this->adminlog("al_adpic", array("do" => "edit", "name" => $name));
                    $ret = DB::getDB()->update("adpic", $data, "picid='$picid'");
                    $this->setHint(__('edit_success', $text), "flink_index");
                }
                break;
            case 'editfield':
                $field = strtolower(trim($_POST["field"]));
                if ($field == "remove") { //删除图片广告
                    $picidstr = $_POST["idstr"];
                    $picids = explode(",", $picidstr);
                    if ($picidstr) {
                        $where = "picid in " . cimplode($picids);
                        $ret = DB::getDB()->update("adpic", "isdel=1", $where);
                        $pics = DB::getDB()->selectkv("adpic", "picid", "name", $where);

                        $recycledata = array();
                        $table = array("table" => "adpic", "type" => "adpic", "tablefield" => "picid", "addtime" => time());
                        foreach ($pics as $picid => $name) {
                            $this->adminlog("al_adpic", array("do" => "remove", "name" => $name));
                            $recycledata[] = $table + array("tableid" => $picid, "title" => $name);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else {//修改友情链接
                    !in_array($field, array("link", "title")) && exit("failure");
                    $picid = intval($_POST["id"]);
                    $field = trim($_POST["field"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_adpic", array("do" => "edit", "picid" => $picid));
                    $ret = DB::getDB()->update("adpic", array($field => $value), "picid='$picid'");
                }
                exit($ret ? "success" : "failure");
                break;
            case 'save':
                $picids = $_POST["picid"];
                foreach ($picids as $key => $picid) {
                    DB::getDB()->update("adpic", array("order" => $key + 1), "picid='$picid'");
                }
                $this->adminlog("al_adpic_order");
                $url = url("admin", "flink", "index");
                $this->setHint(__("edit_success", $text));
                break;
        }
    }

    /**
     *  
     * 首页广告设置
     *
     *
     * */
    public function front()
    {
        $this->data['frontlist'] = DB::getDB()->select("adfront", "title,frontid", "isdel=0", "order");
        $this->output("adfront_list");
    }

    /**
     *  
     * 添加首页广告
     *
     *
     * */
    public function frontadd()
    {
        $this->data["leftcur"] = "flink_front";
        $this->data["opertype"] = "add";
        $this->output("adfront_oper");
    }

    /**
     *  
     * 修改首页广告
     *
     *
     * */
    public function frontedit()
    {
        $this->data["leftcur"] = "flink_front";
        $this->data["opertype"] = "edit";
        $frontid = intval($_GET["frontid"]);
        $this->data["front"] = DB::getDB()->selectrow("adfront", "*", "frontid='$frontid'");
        $this->output("adfront_oper");
    }

    public function frontsave()
    {
        $text = __("frontad");
        $opertype = strtolower($_POST["opertype"]);
        switch ($opertype) {
            case 'add':
            case 'edit':
                $title = trim($_POST["title"]);
                $cont = trim($_POST["content"]);
                $frontid = intval($_POST["frontid"]);
                $data = array("title" => $title, "cont" => $cont);
                if ($frontid) {
                    $this->adminlog("al_adfront", array("do" => "edit", "title" => $title));
                    DB::getDB()->update("adfront", $data, "frontid='$frontid'");
                    $this->setHint(__('edit_success', $text), "flink_front");
                } else {
                    $data["order"] = 50;
                    $this->adminlog("al_adfront", array("do" => "add", "title" => $title));
                    DB::getDB()->insert("adfront", $data);
                    $this->setHint(__("add_success", $text), "flink_front");
                }
                break;
            case 'editfield':
                $field = strtolower(trim($_POST["field"]));
                $ret = false;
                if ($field == "remove") {
                    $frontidstr = $_POST["idstr"];
                    if ($frontidstr) {
                        $frontids = explode(",", $frontidstr);
                        $where = "frontid in " . cimplode($frontids);
                        $ret = DB::getDB()->update("adfront", "isdel=1", $where);
                        $adfronts = DB::getDB()->selectkv("adfront", "frontid", "title", $where);

                        $recycledata = array();
                        $table = array("table" => "adfront", "type" => "frontad", "tablefield" => "frontid", "addtime" => time());
                        foreach ($adfronts as $frontid => $title) {
                            $this->adminlog("al_adfront", array("do" => "remove", "title" => $title));
                            $recycledata[] = $table + array("tableid" => $frontid, "title" => $title);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else {
                    !in_array($field, array("title")) && exit("failure");
                    $frontid = intval($_POST["id"]);
                    $field = trim($_POST["field"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_adfront", array("do" => "edit", "frontid" => $frontid));
                    $ret = DB::getDB()->update("adfront", array($field => $value), "frontid='$frontid'");
                }
                exit($ret ? "success" : "failure");
                break;
            case 'save':
                $frontids = $_POST["frontid"];
                foreach ($frontids as $key => $frontid) {
                    DB::getDB()->update("adfront", array("order" => $key + 1), "frontid='$frontid'");
                }
                $this->adminlog("al_adfront_order");
                $this->setHint(__("edit_success", $text), "flink_front");
                break;
        }
    }

    /**
     *  
     * 热门关键词
     *
     *
     * */
    public function word()
    {
        $this->data['inputword'] = DB::getDB()->selectval("config", "val", array("key" => "inputword"));
        $this->data['wordlist'] = DB::getDB()->select("adword", "*", "isdel=0", "order");
        $this->output("adword_list");
    }

    public function wordadd()
    {
        $this->output("adword_oper");
    }

    public function wordsave()
    {
        $text = __("word");
        $opertype = strtolower($_POST["opertype"]);
        switch ($opertype) {
            case 'add':
                $word = trim($_POST["word"]);
                $link = trim($_POST["link"]);
                $data = array("word" => $word, "link" => $link, "order" => 50);
                $this->adminlog("al_word", array("do" => "add", "word" => $word));
                DB::getDB()->insert("adword", $data);
                $this->setHint(__("add_success", $text), "flink_word");
                break;
            case 'editfield':
                $field = strtolower(trim($_POST["field"]));
                $ret = false;
                if ($field == "remove") {
                    $wordidstr = $_POST["idstr"];
                    if ($wordidstr) {
                        $wordids = explode(",", $wordidstr);
                        $where = "wordid in " . cimplode($wordids);
                        $ret = DB::getDB()->update("adword", "isdel=1", $where);
                        $words = DB::getDB()->selectkv("adword", "wordid", "word", $where);

                        $recycledata = array();
                        $table = array("table" => "adword", "type" => "word", "tablefield" => "wordid", "addtime" => time());
                        foreach ($words as $wordid => $word) {
                            $this->adminlog("al_word", array("do" => "remove", "word" => $word));
                            $recycledata[] = $table + array("tableid" => $wordid, "title" => $word);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else {
                    !in_array($field, array("word", "link")) && exit("failure");
                    $wordid = intval($_POST["id"]);
                    $field = trim($_POST["field"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_word", array("do" => "edit", "wordid" => $wordid));
                    $ret = DB::getDB()->update("adword", array($field => $value), "wordid='$wordid'");
                }
                exit($ret ? "success" : "failure");
                break;
            case 'save':
                $wordids = $_POST["wordid"];
                foreach ($wordids as $key => $wordid) {
                    DB::getDB()->update("adword", array("order" => $key + 1), "wordid='$wordid'");
                }
                $this->adminlog("al_word_order");
                $this->setHint(__("edit_success", $text), "flink_word");
                break;
            case 'inputword':
                $inputword = strip_tags(trim($_POST['inputword']));
                DB::getDB()->replace("config", array("key" => "inputword", "val" => $inputword, "type" => "basicset"));
                $this->adminlog("al_inputword");
                exit("success");
                break;
        }
    }

    private function getAdTag()
    {
        static $ads;
        !$ads && $ads = DB::getDB()->selectcol("adpic", "tag", "", "", "", true);
        return $ads;
    }

}
