<?php

defined('IN_CART') or die;

/**
 *
 * 购买咨询 
 * 
 */
class Review extends Base
{

    /**
     *
     * 购买咨询列表
     * 
     */
    public function index()
    {

        $data = array();

        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $pagesize = isset($_REQUEST["pagesize"]) ? intval($_REQUEST["pagesize"]) : 10;

        $jpara = array("on" => "itemid");
        $where = array("a" => "isdel=0", "b" => "isdel=0");
        $data['pagearr'] = array("page" => $page, "pagesize" => $pagesize);
        $count = DB::getDB()->joincount("user_review", "item", $jpara, $where);
        if ($count) {
            $data["pagearr"] = getPageArr($page, $pagesize, $count);
            $data["reviewlist"] = DB::getDB()->join("user_review", "item", $jpara, array("a" => "*", "b" => "itemname,itemimg"), $where, array("a" => "reviewid DESC"));
        }
        output("review_index", $data);
    }

    /**
     *
     * 保存套餐 
     * 
     */
    public function reviewsave()
    {

        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __("review");
        switch ($opertype) {
            case 'editfield':
                $field = strtolower($_POST["field"]);
                $ret = false;
                if ($field == "remove") {
                    $reviewidstr = trim($_POST["idstr"]);
                    if ($reviewidstr) {
                        $reviewids = explode(",", $reviewidstr);
                        $where = "reviewid in " . cimplode($reviewids);
                        $ret = DB::getDB()->update("user_review", "isdel=1", $where);
                        $titles = DB::getDB()->select("user_review", "reviewcontent,reviewid", $where, null, null, "reviewid");

                        $recycledata = array();
                        $table = array("table" => "user_review", "type" => "userreview", "tablefield" => "reviewid", "addtime" => time());
                        foreach ($reviewids as $reviewid) {
                            $recycledata[] = $table + array("tableid" => $reviewid, "title" => mb_substr($titles[$reviewid]["reviewcontent"], 0, 50, "UTF-8"));
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                    exit($ret ? "success" : "failure");
                } else if ($field == "reply") { //回复
                    $reviewid = intval($_POST["reviewid"]);
                    $reply = trim($_POST["reply"]);
                    $time = time();
                    $ret = DB::getDB()->update("user_review", array("replycontent" => $reply, "replytime" => $time), "reviewid=$reviewid");
                    $jsondata = array("ret" => $ret ? "success" : "failure");
                    if ($ret) {
                        $jsondata += array("replycontent" => $reply);
                    }
                    exit(json_encode($jsondata));
                }
                break;
        }
    }

    /**
     *
     * 购买咨询设置 
     * 
     */
    public function set()
    {

        $data["reviewset"] = DB::getDB()->selectkv("config", "key", "val", "type='reviewset'");
        output("review_set", $data);
    }

    /**
     *
     * 购买咨询设置保存
     * 
     */
    public function setsave()
    {

        $text = __('review');
        $data = array();
        $data['reviewopen'] = intval($_POST["reviewopen"]);
        $data['reviewcaptcha'] = intval($_POST["reviewcaptcha"]);

        $data['reviewshow'] = intval($_POST["reviewshow"]);

        $replacedata = array();
        foreach ($data as $key => $val) {
            $replacedata[] = array("key" => $key, "val" => $val, "type" => 'reviewset');
        }
        DB::getDB()->replaceMulti("config", $replacedata);
        $url = array("referer" => $_SERVER["HTTP_REFERER"]);
        hint(__("set_success", $text), $url);
    }

}
