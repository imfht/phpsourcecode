<?php

defined('IN_CART') or die;

/**
 *  
 * 发件箱模版
 *
 *
 * */
class Sendbox extends Base
{

    /**
     *  
     * email发件箱
     *
     *
     * */
    public function email()
    {
        //分页
        list($page, $pagesize) = $this->getRequestPage();
        $jpara = array("on" => "contentid");
        $where = array("a" => "type='email'");
        $count = DB::getDB()->joincount("notify_sender", "notify_content", $jpara, $where);

        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            $this->data["emaillist"] = DB::getDB()->join("notify_sender", "notify_content", $jpara, array("a" => "contentid,receiver,addtime", "b" => "subject"), $where, array("a" => "contentid DESC"), $this->data['pagearr']['limit']);
        }
        $this->output("sendbox_email");
    }

    /**
     *
     * sms发件箱 
     * 
     */
    public function sms()
    {
        //分页
        list($page, $pagesize) = $this->getRequestPage();

        $jpara = array("on" => "contentid");
        $where = array("a" => "type='sms'");
        $count = DB::getDB()->joincount("notify_sender", "notify_content", $jpara, $where);

        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);

            $this->data["smslist"] = DB::getDB()->join("notify_sender", "notify_content", $jpara, array("a" => "contentid,receiver,addtime", "b" => "content"), $where, array("a" => "contentid DESC"), $this->data['pagearr']['limit']);
        }
        $this->output("sendbox_sms");
    }

    /**
     *
     * 保存结果
     * 
     */
    public function sendboxsave()
    {
        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __("sendbox");
        switch ($opertype) {
            case 'editfield':
                $field = strtolower($_POST["field"]);
                $ret = false;
                if ($field == "delete") { //彻底删除
                    $contentidstr = $_POST["idstr"];
                    if ($contentidstr) {
                        $contentids = explode(",", $contentidstr);
                        $where = "contentid in " . cimplode($contentids);
                        DB::getDB()->delete("notify_queue", $where);
                        DB::getDB()->delete("notify_content", $where);
                    }
                }
                exit("success");
                break;
        }
    }

    /**
     *
     * 已发送的站内信 
     * 
     */
    public function letter()
    {
        list($page, $pagesize) = $this->getRequestPage();
        $count = DB::getDB()->selectcount("letter", "");
        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            $this->data["letterlist"] = DB::getDB()->select("letter", "*", "", "letterid DESC", $this->data['pagearr']['limit']);
        }
        $this->output("letter");
    }

    /**
     *
     * 保存站内信 
     * 
     */
    public function lettersave()
    {
        $opertype = strtolower($_POST["opertype"]);
        $text = __('letter');
        switch ($opertype) {
            case 'editfield':
                $field = strtolower($_POST["field"]);
                if ($field == "delete") {
                    $letteridstr = $_POST["idstr"];
                    if ($letteridstr) {
                        $letterids = explode(",", $letteridstr);
                        DB::getDB()->delete("letter", "letterid in " . cimplode($letterids));
                        exit("success");
                    }
                }
                break;
        }
        exit("failure");
    }

}
