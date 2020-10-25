<?php

defined('IN_CART') or die;

/**
 *  
 * 商品关注
 *
 *
 * */
class Myservice extends Base
{

    private $uid;

    /**
     *  
     * 构造函数，赋值uid
     *
     *
     * */
    public function __construct($model, $action)
    {
        parent::__construct($model, $action);
        if (empty($_SESSION["uid"])) {
            redirect(url("index", 'user', "login"));
        }
        $this->uid = $_SESSION['uid'];
    }

    /**
     *  
     * 我的站内信
     *
     *
     * */
    public function myletter()
    {
        list($page, $pagesize) = $this->getRequestPage();
        $where = "uid='" . $this->uid . "'";
        $count = DB::getDB()->selectcount("letter", $where);
        DB::getDB()->update("user", "unread=0", $where);
        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count, url('index', 'myservice', 'myletter'));
            $this->data["letters"] = DB::getDB()->select("letter", "*", $where, "letterid DESC", $this->data["pagearr"]['limit']);
        }
        $this->output("myletter");
    }

    /**
     *  
     * 删除站内信
     *
     *
     * */
    public function delletter()
    {
        $letterid = intval($_POST["letterid"]);
        DB::getDB()->delete("letter", "letterid='$letterid' AND uid='" . $this->uid . "'");
        exit("success");
    }

    /**
     *  
     * 我的咨询
     *
     *
     * */
    public function myqa()
    {
        list($page, $pagesize) = $this->getRequestPage();

        $where = array("a" => "uid='" . $this->uid . "'", "b" => "isdel=0");
        $jpara = array("on" => "itemid");
        $count = DB::getDB()->joincount("user_qa", "item", $jpara, $where);

        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count, url('index', 'myservice', 'myqa'));
            $this->data["qalist"] = DB::getDB()->join("user_qa", "item", $jpara, array("a" => "*", "b" => "itemname,itemimg"), $where, array("a" => "qaid DESC"), $this->data["pagearr"]["limit"]);
        }
        $this->output("myqa");
    }

    /**
     *  
     * 我的评论
     *
     *
     * */
    public function mycomment()
    {
        list($page, $pagesize) = $this->getRequestPage();

        $where = array("a" => "uid='" . $this->uid . "'", "b" => "isdel=0");
        $jpara = array("on" => "itemid");
        $count = DB::getDB()->joincount("user_comment", "item", $jpara, $where);

        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count, url('index', 'myservice', 'mycomment'));
            $this->data["commentlist"] = DB::getDB()->join("user_comment", "item", $jpara, array("a" => "*", "b" => "itemname,itemimg"), $where, array("a" => "commentid DESC"), $this->data["pagearr"]["limit"]);
        }
        $this->output("mycomment");
    }

    /**
     *  
     * 我的评论
     *
     *
     * */
    public function mycomprice()
    {
        list($page, $pagesize) = $this->getRequestPage();

        $where = "uid='" . $this->uid . "'";
        $count = DB::getDB()->selectcount("user_comment", $where);

        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count, url('index', 'myservice', 'mycomprice'));
            $this->data["compricelist"] = DB::getDB()->select("user_comprice", "*", $where, array("a" => "compriceid DESC"), $this->data["pagearr"]["limit"]);
        }
        $this->output("mycomprice");
    }

    /**
     *  
     * 我的到货通知
     *
     *
     * */
    public function nostock()
    {
        $this->_notify('nostock');
    }

    public function downprice()
    {
        $this->_notify('downprice');
    }

    private function _notify($type)
    {
        list($page, $pagesize) = $this->getRequestPage();

        $where = "isdel=0 AND uid='" . $this->uid . "' AND type='$type'";
        $count = DB::getDB()->selectcount("user_notify", $where);

        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count, url('index', 'myservice', $type));
            $this->data["notifylist"] = DB::getDB()->select("user_notify", "*", $where, "notifyid DESC", $this->data['pagearr']['limit']);
        }
        $this->data["type"] = $type;
        $this->output("mynotify");
    }

}
