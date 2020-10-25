<?php

defined('IN_CART') or die;

/**
 *
 * 评论 
 * 
 */
class Comment extends Base
{

    /**
     *
     * 评论列表
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
        $count = DB::getDB()->joincount("user_comment", "item", $jpara, $where);
        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            $this->data["commentlist"] = DB::getDB()->join("user_comment", "item", $jpara, array("a" => "*", "b" => "itemname,itemimg"), $where, array("a" => "commentid DESC"), $this->data['pagearr']['limit']);
        }
        $this->output("comment_index");
    }

    /**
     *
     * 回复评论 
     * 
     */
    public function commentsave()
    {

        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __("comment");
        switch ($opertype) {
            case 'editfield':
                $field = strtolower($_POST["field"]);
                $ret = false;
                if ($field == "remove") {
                    $commentidstr = trim($_POST["idstr"]);
                    if ($commentidstr) {
                        $commentids = explode(",", $commentidstr);
                        $where = "commentid in " . cimplode($commentids);
                        $ret = DB::getDB()->update("user_comment", "isdel=1", $where);
                        $comments = DB::getDB()->selectkv("user_comment", "commentid", "content", $where);

                        $recycledata = array();
                        $table = array("table" => "user_comment", "type" => "comment", "tablefield" => "commentid", "addtime" => time());
                        foreach ($comments as $commentid => $content) {
                            $recycledata[] = $table + array("tableid" => $commentid, "title" => mb_substr($content, 0, 50, "UTF-8"));
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                    exit($ret ? "success" : "failure");
                } else if ($field == "reply") { //回复
                    $commentid = intval($_POST["commentid"]);
                    $reply = trim($_POST["reply"]);
                    $time = time();
                    $ret = DB::getDB()->update("user_comment", array("replycontent" => $reply, "replytime" => $time), "commentid=$commentid");
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
