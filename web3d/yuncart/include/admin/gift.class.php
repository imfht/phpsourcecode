<?php

defined('IN_CART') or die;

/**
 *
 * 商品赠品
 * 
 */
class Gift extends Base
{

    /**
     *
     * 赠品首页
     *  
     */
    public function index()
    {
        list($page, $pagesize) = $this->getRequestPage();

        $para = array("jtype" => "inner", "on" => "itemid");
        $where = array("b" => "isdel=0");
        $count = DB::getDB()->joincount("gifts", "item", $para, $where);

        if ($count) {
            $this->data['pagearr'] = getPageArr($page, $pagesize, $count);
            $fields = array("a" => "*", "b" => "itemname,itemimg");
            $orderby = array("a" => "giftid DESC");
            $this->data["gifts"] = DB::getDB()->join("gifts", "item", $para, $fields, $where, $orderby, $this->data['pagearr']['limit'], "itemid");
        }
        $this->output("gift_index");
    }

    /**
     *
     * 增加赠品
     *  
     */
    public function giftadd()
    {
        $this->data["opertype"] = "add";
        $this->data["leftcur"] = "gift_index";
        $this->output("gift_oper");
    }

    /**
     *
     * 修改赠品
     *  
     */
    public function giftedit()
    {
        $giftid = intval($_GET["giftid"]);
        $this->data["opertype"] = "edit";

        $para = array("jtype" => "inner", "on" => "itemid");
        $fields = array("a" => "*", "b" => "itemname,itemimg");
        $where = array("b" => "isdel=0", "a" => "giftid='$giftid'");

        $this->data["gift"] = DB::getDB()->joinrow("gifts", "item", $para, $fields, $where);
        $this->data["giftid"] = $giftid;
        $this->output("gift_oper");
    }

    /**
     *
     * 保存赠品
     *  
     */
    public function giftsave()
    {
        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __('gift');
        switch ($opertype) {
            case 'add':
            case 'edit':
                //接受参数
                $subject = trim($_POST["subject"]);
                $itemid = intval($_POST["itemid"]);
                $nums = empty($_POST["num"]) ? array() : $_POST["num"];
                $begintime = trim($_POST["begintime"]);
                $endtime = trim($_POST["endtime"]);

                //提取商品信息
                $gitemids = array_keys($nums);
                $allitemids = array_merge($gitemids, array($itemid));
                $items = DB::getDB()->select("item", "itemid,itemname,itemimg,price", "itemid in " . cimplode($allitemids), "", "", "itemid");

                $data = array("itemid" => $itemid,
                    "subject" => $subject,
                    "ispublish" => 1,
                    "begintime" => strtotime($begintime),
                    "endtime" => strtotime($endtime)
                );
                $gifts = array();
                foreach ($gitemids as $gitemid) {
                    $gifts[] = array("gitemid" => $gitemid,
                        "gitemname" => $items[$gitemid]["itemname"],
                        "gitemimg" => $items[$gitemid]["itemimg"],
                        "num" => $nums[$gitemid]);
                }
                $data["gift"] = serialize($gifts);
                if ($opertype == "add") { //增加赠品
                    DB::getDB()->replace("gifts", $data);
                    $this->adminlog("al_gift", array("do" => "add", "subject" => $subject));
                    $this->setHint(__("add_success", $text));
                } elseif ($opertype == "edit") { //选择赠品
                    $giftid = intval($_POST["giftid"]);
                    $this->adminlog("al_gift", array("do" => "edit", "subject" => $subject));
                    DB::getDB()->update("gifts", $data, "giftid='$giftid'");
                    $this->setHint(__("edit_success", $text));
                }
                break;
            case 'editfield':
                $field = strtolower(trim($_REQUEST["field"]));
                if ($field == "delete") { //删除赠品
                    $giftidstr = $_POST["idstr"];
                    if ($giftidstr) {
                        $giftids = explode(",", $giftidstr);
                        $where = "giftid in " . cimplode($giftids);

                        $gifts = DB::getDB()->selectkv("gifts", "giftid", "subject", $where);
                        foreach ($gifts as $subject) {
                            $this->adminlog("al_gift", array("do" => "del", "subject" => $subject));
                        }
                        DB::getDB()->delete("gifts", $where);
                        exit("success");
                    }
                } else if ($field == "publish") { //设置发布属性
                    $giftid = intval($_GET["giftid"]);
                    $ret = DB::getDB()->updatebool("gifts", "ispublish", "giftid='$giftid'");
                    $this->adminlog("al_gift", array("do" => "edit", "giftid" => $giftid));
                    $this->setHint(__('set_success', array($text, __('publish_property'))));
                }
                exit("failure");
                break;
        }
    }

    /**
     *
     * 相关订单
     *  
     */
    public function order()
    {
        $itemid = isset($_GET["itemid"]) ? intval($_GET["itemid"]) : 0;
        //分页
        list($page, $pagesize) = $this->getRequestPage();

        $where['type'] = 'gift';
        if ($itemid)
            $where["typeid"] = $itemid;

        $onarr = array("on" => "typeid,itemid");
        $count = DB::getDB()->joincount("trade_promotion", "item", $onarr, array("a" => $where));
        if ($count) {
            //获取分页参数
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            //查询数据
            $this->data["records"] = DB::getDB()->join("trade_promotion", "item", $onarr, array("a" => "*", "b" => "itemimg,itemname"), array("a" => $where), array("a" => "addtime DESC"), $this->data['pagearr']['limit']);
            $tradeids = array();
            foreach ($data['records'] as $record) {
                $tradeids[] = $record['tradeid'];
            }
            if ($tradeids) {
                $this->data['trades'] = DB::getDB()->selectkv("trade", "tradeid", "status", "tradeid in " . cimplode($tradeids));
            }
        }
        $this->output("gift_order");
    }

}
