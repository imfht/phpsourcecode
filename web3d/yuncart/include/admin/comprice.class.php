<?php

defined('IN_CART') or die;

/**
 *
 * 评论 
 * 
 */
class Comprice extends Base
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
        $where['isdel'] = 0;
        //搜索
        $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : "";
        $qtype = isset($_REQUEST['qtype']) ? trim($_REQUEST['qtype']) : "item";
        $this->data['q'] = $q;
        $this->data['qtype'] = $qtype;
        if ($qtype == 'user') {
            is_numeric($q) && ($where['uid'] = $q) || ($where['uname'] = "like '%" . $q . "%'");
        } elseif ($qtype == 'item') {
            is_numeric($q) && ($where['itemid'] = $q) || ($where['itemname'] = "like '%" . $q . "%'");
        } elseif ($qtype == 'content') {
            $where['a']['content'] = "like '%" . $q . "%'";
        }

        $count = DB::getDB()->selectcount("user_comprice", $where);
        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            $this->data["compricelist"] = DB::getDB()->select("user_comprice", "*", $where, array("a" => "compriceid DESC"), $this->data['pagearr']['limit']);
        }
        $this->output("comprice_index");
    }

    /**
     *
     * 回复评论 
     * 
     */
    public function compricesave()
    {

        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __("comprice");
        switch ($opertype) {
            case 'editfield':
                $field = strtolower($_POST["field"]);
                $ret = false;
                if ($field == "remove") {
                    $compriceidstr = trim($_POST["idstr"]);
                    if ($compriceidstr) {
                        $compriceids = explode(",", $compriceidstr);
                        $where = "compriceid in " . cimplode($compriceids);
                        $ret = DB::getDB()->update("user_comprice", "isdel=1", $where);
                        $comprices = DB::getDB()->selectkv("user_comprice", "compriceid", "content", $where);

                        $recycledata = array();
                        $table = array("table" => "user_comprice", "type" => "comprice", "tablefield" => "compriceid", "addtime" => time());
                        foreach ($comprices as $compriceid => $content) {
                            $recycledata[] = $table + array("tableid" => $compriceid, "title" => mb_substr($content, 0, 50, "UTF-8"));
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                    exit($ret ? "success" : "failure");
                } else if ($field == "reply") { //回复
                    $compriceid = intval($_POST["compriceid"]);
                    $reply = trim($_POST["reply"]);
                    $time = time();
                    $ret = DB::getDB()->update("user_comprice", array("replycontent" => $reply, "replytime" => $time), "compriceid='$compriceid'");
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
