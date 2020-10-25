<?php

defined('IN_CART') or die;

/**
 *  
 * 栏目管理
 * idtype 1 链接地址 2 文章 3 商品类别
 *
 * */
class Page extends Base
{

    /**
     *  
     * 栏目列表
     *
     *
     * */
    public function index()
    {
        $this->data["pagelist"] = DB::getDB()->select("page", "*", "isdel=0", "pageid DESC");
        $this->data["weburl"] = getConfig("weburl");
        $this->output("page_index");
    }

    /**
     *  
     * 增加栏目
     *
     * */
    public function pageadd()
    {
        $this->data["opertype"] = "add";
        $this->data["leftcur"] = "page_index";
        $this->output("page_oper");
    }

    /**
     *  
     * 修改栏目
     *
     * */
    public function pageedit()
    {
        $pageid = intval($_GET["pageid"]);
        $this->data["opertype"] = "edit";
        $this->data["pageid"] = $pageid;
        $this->data["page"] = DB::getDB()->selectrow("page", "*", "pageid='$pageid'");
        $this->output("page_oper");
    }

    /**
     *  
     * 保存栏目
     *
     * */
    public function pagesave()
    {
        $opertype = strtolower($_POST["opertype"]);
        $text = __('page');
        switch ($opertype) {
            case 'add':
            case 'edit':
                $pagetitle = trim($_POST["pagetitle"]);
                $keywords = trim($_POST["keywords"]);
                $description = trim($_POST["description"]);
                $content = trim($_POST["content"]);

                $data = array("pagetitle" => $pagetitle, "keywords" => $keywords, "description" => $description, "content" => $content);

                if ($opertype == "add") {
                    $navi = isset($_POST["navi"]) ? 1 : 0;
                    $pageid = DB::getDB()->insert("page", $data);
                    if ($navi) {
                        $naviname = trim($_POST["naviname"]);
                        !$naviname && $naviname = $pagetitle;
                        DB::getDB()->insert("navi", array(
                            "naviname" => $naviname,
                            "tabletype" => 1,
                            "naviurl" => url('index', 'content', 'page', 'pageid=' . $pageid),
                            "order" => 50
                        ));
                    }
                    $this->adminlog("al_page", array("do" => "add", "pagetitle" => $pagetitle));
                    $this->setHint(__("add_success", $text));
                } else {
                    $pageid = intval($_POST["pageid"]);
                    $this->adminlog("al_page", array("do" => "edit", "pagetitle" => $pagetitle));
                    $ret = DB::getDB()->update("page", $data, "pageid='$pageid'");
                    $this->setHint(__("edit_success", $text));
                }
                break;
            case 'editfield':
                $field = strtolower($_POST["field"]);
                $ret = false;
                if ($field == "remove") {
                    $pageidstr = $_POST["idstr"];
                    $pageids = explode(",", $pageidstr);
                    if ($pageidstr) {
                        $where = "pageid in " . cimplode($pageids);
                        $ret = DB::getDB()->update("page", "isdel=1", $where);
                        $titles = DB::getDB()->selectkv("page", "pageid", "pagetitle", $where);

                        $recycledata = array();
                        $table = array("table" => "page", "type" => "page", "tablefield" => "pageid", "addtime" => time());
                        foreach ($pageids as $pageid) {
                            $this->adminlog("al_page", array("do" => "remove", "pagetitle" => $titles[$pageid]));
                            $recycledata[] = $table + array("tableid" => $pageid, "title" => $titles[$pageid]);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else {
                    !in_array($field, array("pagetitle")) && exit("success");
                    $pageid = intval($_POST["id"]);
                    $field = trim($_POST["field"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_page", array("do" => "edit", "pageid" => $pageid));
                    $ret = DB::getDB()->update("page", array($field => $value), "pageid='$pageid'");
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

}
