<?php

defined('IN_CART') or die;

/**
 *  
 * 
 * 团购商品
 * 
 *
 * */
class Tuan extends Base
{

    /**
     *  
     * 团购首页
     *
     * */
    public function index()
    {
        list($page, $pagesize) = $this->getRequestPage();
        $para = array("jtype" => "inner", "on" => "itemid");
        $where = array("b" => "isdel=0");
        $count = DB::getDB()->joincount("tuan", "item", $para, $where);

        if ($count) {
            //获取分页参数
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            $fields = array("a" => "*", "b" => "itemname,itemimg");
            $orderby = array("a" => "tuanid DESC");

            $this->data["tuans"] = DB::getDB()->join("tuan", "item", $para, $fields, $where, $orderby, $this->data['pagearr']['limit'], "itemid");
        }
        $this->output("tuan_index");
    }

    /**
     *  
     * 添加团购
     *
     * */
    public function tuanadd()
    {
        $this->data["opertype"] = "add";
        $this->data['leftcur'] = "tuan_index";
        $this->output("tuan_oper");
    }

    /**
     *  
     * 修改团购
     *
     * */
    public function tuanedit()
    {
        $tuanid = intval($_GET["tuanid"]);
        $wherestr = "tuanid='$tuanid'";

        $para = array("jtype" => "inner", "on" => "itemid");
        $fields = array("a" => "*", "b" => "itemname,itemimg");
        $where = array("b" => "isdel=0", "a" => $wherestr);

        $this->data["tuan"] = DB::getDB()->joinrow("tuan", "item", $para, $fields, $where);
        if ($this->data["tuan"]) {
            $this->data["tuandesc"] = DB::getDB()->selectrow("tuan_desc", "*", $wherestr);
        }

        $this->data["tuanid"] = $tuanid;
        $this->data["opertype"] = "edit";
        $this->data['leftcur'] = "tuan_index";
        $this->output("tuan_oper");
    }

    /**
     *  
     * 保存团购
     *
     * */
    public function tuansave()
    {
        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __("tuan");

        switch ($opertype) {
            case 'add':
            case 'edit':
                $subject = trim($_POST["subject"]);
                $itemid = intval($_POST["itemid"]);
                $begintime = strtotime($_POST["begintime"]);
                $endtime = strtotime($_POST["endtime"]);
                $price = getPrice($_POST["price"], 2, "int");
                $desc = trim($_POST["desc"]);
                !$desc && ($desc = "");


                $data = array("itemid" => $itemid, "begintime" => $begintime, "endtime" => $endtime, "price" => $price, "subject" => $subject);
                $descdata = array("desc" => $desc);
                $tuanid = intval($_POST["tuanid"]);

                if ($tuanid) { //修改
                    $where = "tuanid='$tuanid'";
                    DB::getDB()->update("tuan", $data, $where);
                    DB::getDB()->update("tuan_desc", $descdata, $where);
                    $this->adminlog("al_tuan", array("do" => "edit", "subject" => $subject));
                    $this->setHint(__("edit_success", $text));
                } else { //添加
                    $tuanid = DB::getDB()->insert("tuan", $data);
                    DB::getDB()->insert("tuan_desc", $descdata + array("tuanid" => $tuanid));
                    $this->adminlog("al_tuan", array("do" => "add", "subject" => $subject));
                    $this->setHint(__("add_success", $text));
                }
                break;
            case 'editfield':
                $field = strtolower($_REQUEST["field"]);
                if ($field == "delete") {
                    $tuanidstr = trim($_POST["idstr"]);
                    if ($tuanidstr) {
                        $tuanids = explode(",", $tuanidstr);
                        $where = "tuanid in " . cimplode($tuanids);
                        $tuans = DB::getDB()->selectkv("tuan", "tuanid", "subject", $where);
                        foreach ($tuans as $subject) {
                            $this->adminlog("al_tuan", array("do" => "del", "subject" => $subject));
                        }
                        DB::getDB()->delete("tuan", $where);
                        DB::getDB()->delete("tuan_desc", $where);
                        exit("success");
                    }
                } else if ($field == "publish") {
                    $tuanid = intval($_GET["tuanid"]);
                    $ret = DB::getDB()->updatebool("tuan", "ispublish", "tuanid='$tuanid'");
                    $this->setHint(__('set_success', array($text, __('publish_property'))));
                }
                exit("failure");
                break;
        }
    }

}
