<?php

defined('IN_CART') or die;

/**
 *  
 * 友情连接
 *
 *
 * */
class Link extends Base
{

    /**
     *  
     * 友链列表
     *
     *
     * */
    public function index()
    {
        $where = "isdel=0";
        $this->data["linklist"] = DB::getDB()->select("link", "*", "isdel=0", "order");
        $this->output("link_index");
    }

    /**
     *  
     * 增加友链
     *
     *
     * */
    public function linkadd()
    {
        $this->data["opertype"] = "add";
        $this->data["leftcur"] = "link_index";
        $this->output("link_oper");
    }

    /**
     *  
     * 修改友链
     *
     *
     * */
    public function linkedit()
    {
        $linkid = intval($_GET["linkid"]);
        $this->data["opertype"] = "edit";
        $this->data["linkid"] = $linkid;
        $this->data["link"] = DB::getDB()->selectrow("link", "*", "linkid='$linkid'");
        $this->output("link_oper");
    }

    /**
     *  
     * 保存友链
     *
     *
     * */
    public function linksave()
    {
        $text = __("link");
        $opertype = strtolower($_POST["opertype"]);
        switch ($opertype) {
            case 'add':
            case 'edit':
                $linkname = $_POST["linkname"];
                $linkurl = $_POST["linkurl"];
                $linkpic = $_POST["pic"];
                $data = array("linkname" => $linkname, "linkurl" => $linkurl, "linkpic" => $linkpic);

                if ($opertype == "add") {
                    $data["order"] = 1000;
                    $this->adminlog("al_link", array("do" => "add", "linkname" => $linkname));
                    $ret = DB::getDB()->insert("link", $data);
                    $this->setHint(__('add_success', $text));
                } else {
                    $linkid = intval($_POST["linkid"]);
                    $this->adminlog("al_link", array("do" => "edit", "linkname" => $linkname));
                    $ret = DB::getDB()->update("link", $data, "linkid='$linkid'");
                    $this->setHint(__('edit_success', $text));
                }
                break;
            case 'editfield':
                $field = strtolower(trim($_POST["field"]));
                $ret = false;
                if ($field == "remove") {  //删除友情链接
                    $linkidstr = $_POST["idstr"];
                    $linkids = explode(",", $linkidstr);
                    if ($linkidstr) {
                        $where = "linkid in " . cimplode($linkids);
                        $ret = DB::getDB()->update("link", "isdel=1", $where);
                        $titles = DB::getDB()->selectkv("link", "linkid", "linkname", $where);

                        $recycledata = array();
                        $table = array("table" => "link", "type" => "link", "tablefield" => "linkid", "addtime" => time());
                        foreach ($linkids as $linkid) {
                            $this->adminlog("al_link", array("do" => "remove", "linkname" => $titles[$linkid]));
                            $recycledata[] = $table + array("tableid" => $linkid, "title" => $titles[$linkid]);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else {//修改友情链接名称
                    !in_array($field, array("linkname")) && exit("failure");
                    $linkid = intval($_POST["id"]);
                    $field = trim($_POST["field"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_link", array("do" => "edit", "linkid" => $linkid));
                    $ret = DB::getDB()->update("link", array($field => $value), "linkid='$linkid'");
                }
                exit($ret ? "success" : "failure");
                break;
            case 'save':
                $linkids = $_POST["linkid"];
                foreach ($linkids as $key => $linkid) {
                    DB::getDB()->update("link", array("order" => $key + 1), "linkid='$linkid'");
                }
                $this->adminlog("al_link_order");
                $this->setHint(__("edit_success", $text));
                break;
        }
    }

}
