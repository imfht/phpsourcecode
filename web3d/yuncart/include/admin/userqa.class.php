<?php

defined('IN_CART') or die;

/**
 *
 * 购买咨询 
 * 
 */
class Userqa extends Base
{

    /**
     *
     * 购买咨询列表
     * 
     */
    public function index()
    {
        //分页
        list($page, $pagesize) = $this->getRequestPage();

        //条件
        $where['a']['isdel'] = 0;
        $where['b']['isdel'] = 0;
        //搜索
        $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : "";
        $qtype = isset($_REQUEST['qtype']) ? trim($_REQUEST['qtype']) : "item";
        $this->data['q'] = $q;
        $this->data['qtype'] = $qtype;
        if ($qtype == 'user') {
            is_numeric($q) && ($where['a']['uid'] = $q) || ($where['a']['uname'] = "like '%" . $q . "%'");
        } elseif ($qtype == 'item') {
            is_numeric($q) && ($where['b']['itemid'] = $q) || ($where['b']['itemname'] = "like '%" . $q . "%'");
        } elseif ($qtype == 'content') {
            $where['a']['content'] = "like '%" . $q . "%'";
        }
        $jpara = array("on" => "itemid");
        $count = DB::getDB()->joincount("user_qa", "item", $jpara, $where);
        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            $this->data["qalist"] = DB::getDB()->join("user_qa", "item", $jpara, array("a" => "*", "b" => "itemname,itemimg"), $where, array("a" => "qaid DESC"), $this->data["pagearr"]["limit"]);
        }
        $this->output("userqa_index");
    }

    /**
     *
     *  回复咨询
     * 
     */
    public function userqasave()
    {
        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __("userqa");
        switch ($opertype) {
            case 'editfield':
                $field = strtolower($_POST["field"]);
                $ret = false;
                if ($field == "remove") {
                    $qaidstr = trim($_POST["idstr"]);
                    if ($qaidstr) {
                        $qaids = explode(",", $qaidstr);
                        $where = "qaid in " . cimplode($qaids);
                        $ret = DB::getDB()->update("user_qa", "isdel=1", $where);
                        $qas = DB::getDB()->selectkv("user_qa", "qaid", "content", $where);

                        $recycledata = array();
                        $table = array("table" => "user_qa", "type" => "userqa", "tablefield" => "qaid", "addtime" => time());
                        foreach ($qas as $qaid => $content) {
                            $recycledata[] = $table + array("tableid" => $qaid, "title" => mb_substr($content, 0, 50, "UTF-8"));
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                    exit($ret ? "success" : "failure");
                } else if ($field == "reply") { //回复
                    $qaid = intval($_POST["qaid"]);
                    $reply = trim($_POST["reply"]);
                    $time = time();
                    $ret = DB::getDB()->update("user_qa", array("replycontent" => $reply, "replytime" => $time), "qaid='$qaid'");
                    $jsondata = array("ret" => $ret ? "success" : "failure");
                    if ($ret) {
                        $jsondata += array("replycontent" => $reply);
                    }
                    exit(json_encode($jsondata));
                }
                break;
        }
    }

}
