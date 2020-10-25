<?php

defined('IN_CART') or die;

/**
 *  
 * 商品关注
 *
 *
 * */
class Favor extends Base
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
     * 我的关注
     *
     *
     * */
    public function myFavor()
    {
        list($page, $pagesize) = $this->getRequestPage();

        $where = array("a" => "uid='" . $this->uid . "'", "b" => "isdel=0");
        $jpara = array("on" => "itemid");
        $count = DB::getDB()->joincount("user_favor", "item", $jpara, $where);
        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count, url('index', 'favor', 'myfavor'));
            $this->data["items"] = DB::getDB()->join("user_favor", "item", $jpara, array("a" => "addtime", "b" => "itemid,itemimg,itemname,price"), $where, array("a" => "addtime DESC"), $this->data['pagearr']['limit']);
        }
        $this->output("myfavor");
    }

    /**
     *  
     * 删除关注
     *
     *
     * */
    public function del()
    {
        $itemid = intval($_POST["itemid"]);
        DB::getDB()->delete("user_favor", "itemid='$itemid' AND uid='" . $this->uid . "'");
        exit("success");
    }

}
