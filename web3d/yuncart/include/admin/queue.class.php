<?php

defined('IN_CART') or die;

/**
 *
 * 邮件短信队列
 * 
 */
class Queue extends Base
{

    /**
     *
     * 邮件队列
     * 
     */
    public function email()
    {
        $jpara = array("on" => "contentid");
        $where = array("a" => "type='email'");

        //只提取前10条
        $this->data['count'] = DB::getDB()->joincount("notify_queue", "notify_content", $jpara, $where);
        if ($this->data['count']) {
            $this->data["queueemail"] = DB::getDB()->join("notify_queue", "notify_content", $jpara, array("a" => "contentid,notify,addtime,msg", "b" => "subject"), $where, array("a" => "contentid DESC"), "10");
        }
        $this->output("queue_email");
    }

    /**
     *
     * 短信队列
     * 
     */
    public function sms()
    {
        $jpara = array("on" => "contentid");
        $where = array("a" => "type='sms'");
        $this->data['count'] = DB::getDB()->joincount("notify_queue", "notify_content", $jpara, $where);
        if ($this->data['count']) {
            $this->data["queuesms"] = DB::getDB()->join("notify_queue", "notify_content", $jpara, array("a" => "contentid,notify,addtime,msg", "b" => "content"), $where, array("a" => "contentid DESC"), "10");
        }
        $this->output("queue_sms");
    }

    /**
     *
     * 保存操作队列
     * 
     */
    public function queuesave()
    {
        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __("queue");
        switch ($opertype) {
            case 'editfield':
                $field = strtolower($_POST["field"]);
                $ret = false;
                if ($field == "delete") {
                    $contentidstr = $_POST["idstr"];
                    if ($contentidstr) {
                        $contentids = explode(",", $contentidstr);
                        $where = "contentid in " . cimplode($contentids);
                        DB::getDB()->delete("notify_queue", $where);
                        DB::getDB()->delete("notify_content", $where);
                    }
                } elseif ($field == "empty") {
                    $type = trim($_POST["type"]);
                    $where = "type='" . $type . "'";
                    DB::getDB()->delete("notify_queue", $where);
                    DB::getDB()->delete("notify_content", $where . " AND issend = 0");
                }
                exit('success');
                break;
        }
    }

}
