<?php

defined('IN_CART') or die;

/**
 * 网店文章管理
 * type =1 帮助中心 
 * type =2 网店公告
 *
 * */
class Content extends Base
{

    /**
     *
     * 帮助中心类别
     * 
     */
    public function helpsort()
    {
        $this->sortlist(1);
        $this->output("helpsort_index");
    }

    /**
     *
     * 添加一个帮助中心类别
     * 
     */
    public function helpsortadd()
    {
        $this->data["opertype"] = "add";
        $this->output("helpsort_oper");
    }

    /**
     *
     * 保存帮助中心类别
     * 
     */
    public function helpsortsave()
    {
        $this->sortsave(1);
    }

    /**
     *
     * 网店公告类别
     * 
     */
    public function noticesort()
    {
        $this->sortlist(2);
        $this->output("noticesort_index");
    }

    /**
     *
     * 增加网店公告类别
     * 
     */
    public function noticesortadd()
    {
        $this->data["opertype"] = "add";
        $this->output("noticesort_oper");
    }

    /**
     *
     * 保存网店公告类别
     * 
     */
    public function noticesortsave()
    {
        $this->sortsave(2);
    }

    /**
     *
     * 公用类别列表
     * 
     */
    private function sortlist($type)
    {
        $where = "`type`='$type' AND isdel=0";
        $this->data['contentsort'] = DB::getDB()->select("content_sort", "*", $where, "order");
        $contents = DB::getDB()->selectgroup("content", "sortid", "sortid", "count", "", $where, "sortid");
        foreach ($this->data["contentsort"] as $key => $val) {
            $this->data["contentsort"][$key]["num"] = isset($contents[$val["sortid"]]) ? $contents[$val["sortid"]]['countval'] : 0;
        }
    }

    /**
     *
     * 公用保存类别
     * 
     */
    private function sortsave($type)
    {
        $typetext = $type == 1 ? "help" : "notice";
        $text = __("{$typetext}_sort");
        $opertype = trim($_REQUEST["opertype"]);

        switch ($opertype) {
            case 'add':
                $sortname = trim($_POST["sortname"]);
                $data = array("sortname" => $sortname, "type" => $type, "order" => 1000);
                $this->adminlog("al_{$typetext}sort", array("do" => "add", "sortname" => $sortname));

                $ret = DB::getDB()->insert("content_sort", $data);
                $this->setHint(__("add_success", $text), "content_{$typetext}sort");
                break;
            case 'editfield':
                $field = strtolower(trim($_POST["field"]));
                $ret = false;
                if ($field == "remove") {
                    $sortidstr = trim($_POST["idstr"]);
                    if ($sortidstr) {
                        $sortids = explode(",", $sortidstr);
                        $where = "sortid in " . cimplode($sortids);
                        $ret = DB::getDB()->update("content_sort", "isdel=1", $where);
                        $titles = DB::getDB()->selectkv("content_sort", "sortid", "sortname", $where);

                        $recycledata = array();
                        $table = array("table" => "content_sort", "type" => "{$typetext}_sort", "tablefield" => "sortid", "addtime" => time());
                        foreach ($sortids as $sortid) {
                            $this->adminlog("al_{$typetext}sort", array("do" => "del", "sortname" => $titles[$sortid]));
                            $recycledata[] = $table + array("tableid" => $sortid, "title" => $titles[$sortid]);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                        exit($ret ? "success" : "failure");
                    }
                } else {
                    !in_array($field, array("sortname")) && exit("failure");
                    $sortid = intval($_POST["id"]);
                    $field = trim($_POST["field"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_{$typetext}sort", array("do" => "edit", "sortid" => $sortid));
                    $ret = DB::getDB()->update("content_sort", array($field => $value), "sortid='$sortid'");
                }
                exit($ret ? "success" : "failure");
                break;
            case 'save':
                $sortids = $_POST["sortid"];
                foreach ($sortids as $key => $sortid) {
                    DB::getDB()->update("content_sort", array("order" => $key + 1), "sortid='$sortid'");
                }
                $this->adminlog("al_{$typetext}sort_order");
                $this->setHint(__("edit_success", $text), "content_{$typetext}sort");
                break;
        }
    }

    /**
     *
     * 公告文章列表
     * 
     */
    public function noticelist()
    {
        $this->contentlist(2);
        $this->output("notice_list");
    }

    /**
     *
     * 帮助文章列表
     * 
     */
    public function helplist()
    {
        $this->contentlist(1);
        $this->output("help_list");
    }

    /**
     *
     * 公用文章列表
     * 
     */
    private function contentlist($type)
    {
        list($page, $pagesize) = $this->getRequestPage();

        $sortid = isset($_REQUEST['seasortid']) ? intval($_REQUEST["seasortid"]) : 0;

        $where = array();
        $sortid && $where['b']['sortid'] = $sortid;
        $where['a']['type'] = $type;
        $where['b']['isdel'] = 0;

        //搜索
        $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : "";
        $this->data['q'] = $q;
        if ($q) {
            is_numeric($q) && ($where['b']['contentid'] = $q) || ($where['b']['subject'] = "like '%" . $q . "%'");
        }

        $joinpara = array("on" => "sortid");
        $count = DB::getDB()->joincount("content_sort", "content", $joinpara, $where);

        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            $this->data["contents"] = DB::getDB()->join("content_sort", "content", $joinpara, array("a" => "sortname", "b" => "*"), $where, array("a" => "order", "b" => "order,sortid"), $this->data["pagearr"]["limit"]);
        }

        $this->data['sortid'] = $sortid;
        $this->data["sortopt"] = $this->getContentSort($type, $sortid);
    }

    /**
     *
     * 增加帮助文章
     * 
     */
    public function helpadd()
    {
        $this->contentadd(1);
        $this->output("help_oper");
    }

    /**
     *
     * 增加公告文章
     * 
     */
    public function noticeadd()
    {
        $this->contentadd(2);
        $this->output("notice_oper");
    }

    /**
     *
     * 修改帮助文章
     * 
     */
    public function helpedit()
    {
        $this->contentedit(1);
        $this->output("help_oper");
    }

    /**
     *
     * 修改公告文章
     * 
     */
    public function noticeedit()
    {
        $this->contentedit(2);
        $this->output("notice_oper");
    }

    /**
     *
     * 保存帮助文章
     * 
     */
    public function helpsave()
    {
        $this->contentsave(1);
    }

    /**
     *
     * 保存公告文章
     * 
     */
    public function noticesave()
    {
        $this->contentsave(2);
    }

    /**
     *
     * 公用增加文章
     * 
     */
    public function contentadd($type)
    {
        $this->data["opertype"] = "add";
        $this->data["sortopt"] = $this->getContentSort($type);
        $this->data["leftcur"] = $type == 1 ? "content_helplist" : "content_noticelist";
    }

    /**
     *
     * 公用修改文章
     * 
     */
    public function contentedit($type)
    {
        $contentid = intval($_GET["contentid"]);

        $this->data["opertype"] = "edit";
        $this->data["content"] = DB::getDB()->selectrow("content", "*", "contentid='$contentid'");
        $this->data["contentid"] = $contentid;
        $this->data["sortopt"] = $this->getContentSort($type, $this->data['content']['sortid']);
        $this->data["leftcur"] = $type == 1 ? "content_helplist" : "content_noticelist";
    }

    /**
     *
     * 公用保存文章
     * 
     */
    public function contentsave($type)
    {

        $opertype = strtolower($_REQUEST["opertype"]);
        $typetext = $type == 1 ? "help" : "notice";
        $text = __("{$typetext}_article");


        switch ($opertype) {
            case 'add':
            case 'edit':
                //参数
                $subject = $_POST["subject"];
                $contenttype = $_POST["contenttype"];
                $sortid = intval($_POST["sortid"]);
                $order = intval($_POST["order"]);
                $time = time();
                $data = array("subject" => $subject, "sortid" => $sortid, "type" => $type, 'contenttype' => $contenttype, "order" => $order);
                //文章类型
                if ($contenttype == "link") {
                    $data += array("content" => '', "link" => $_POST["link"]);
                } elseif ($contenttype == "cont") {
                    $data += array("content" => $_POST["content"], "link" => '');
                }

                if ($opertype == "add") { //添加
                    $data['addtime'] = $time;
                    $ret = DB::getDB()->insert("content", $data);
                    $this->adminlog("al_{$typetext}article", array("do" => "add", "subject" => $subject));
                    $this->setHint(__("add_success", $text), "content_{$typetext}list");
                } else {    //修改
                    $data['modifytime'] = $time;
                    $contentid = intval($_POST["contentid"]);
                    $ret = DB::getDB()->update("content", $data, "contentid='$contentid'");
                    $this->adminlog("al_{$typetext}article", array("do" => "edit", "subject" => $subject));
                    $this->setHint(__("edit_success", $text), "content_{$typetext}list");
                }
                break;
            case 'editfield':
                $ret = false;
                $field = strtolower(trim($_REQUEST["field"]));
                if ($field == "remove") {//移除
                    $contentidstr = $_POST["idstr"];
                    $contentids = explode(",", $contentidstr);
                    if ($contentidstr) {
                        $where = "contentid in " . cimplode($contentids);
                        $ret = DB::getDB()->update("content", "isdel=1", $where);
                        $titles = DB::getDB()->selectkv("content", "contentid", "subject", $where);

                        $recycledata = array();
                        $table = array("table" => "content", "type" => "{$typetext}_article", "tablefield" => "contentid", "addtime" => time());
                        foreach ($contentids as $contentid) {
                            $this->adminlog("al_{$typetext}article", array("do" => "remove", "subject" => $titles[$contentid]));
                            $recycledata[] = $table + array("tableid" => $contentid, "title" => $titles[$contentid]);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else if ($field == "publish") {//修改发布状态
                    $contentid = intval($_GET["contentid"]);
                    $ret = DB::getDB()->updatebool("content", "ispublish", "contentid='$contentid'");
                    $this->adminlog("al_{$typetext}article", array("do" => "edit", "contentid" => $contentid));
                    $this->setHint(__('set_success', array($text, __('publish_property'))), "content_{$typetext}list");
                } else { //修改
                    !in_array($field, array("subject", "order")) && exit("failure");
                    $contentid = intval($_POST["id"]);
                    $field = trim($_POST["field"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_{$typetext}article", array("do" => "edit", "contentid" => $contentid));
                    $ret = DB::getDB()->update("content", array($field => $value), "contentid='$contentid'");
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

}
