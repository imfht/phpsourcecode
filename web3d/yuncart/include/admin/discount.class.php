<?php

defined('IN_CART') or die;

/**
 *  
 * 限时折扣
 *
 *
 * */
class Discount extends Base
{

    /**
     *  
     * 限时折扣
     *
     *
     * */
    public function index()
    {
        list($page, $pagesize) = $this->getRequestPage();
        $count = DB::getDB()->selectcount("discount");
        if ($count) {
            //获取分页参数
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);

            //查询数据
            $this->data["discounts"] = DB::getDB()->select("discount", "*", "", "discountid DESC", $this->data['pagearr']['limit']);
        }
        $this->output("discount_index");
    }

    /**
     *
     * 增加 
     * 
     */
    public function discountadd()
    {
        $this->data["opertype"] = "add";
        $this->data["leftcur"] = "discount_index";
        $this->output("discount_oper");
    }

    /**
     *
     * 修改 
     * 
     */
    public function discountedit()
    {
        $discountid = intval($_GET["discountid"]);
        $this->data["opertype"] = "edit";
        $this->data['discount'] = DB::getDB()->selectrow("discount", "*", "discountid='$discountid'");
        $this->data["discountitems"] = DB::getDB()->join("discount_item", "item", array("on" => "itemid"), array("a" => "discountid,itemid,limit,discount", "b" => "itemimg,price,itemname"), array("a" => "discountid='$discountid'"));
        $this->data["discountid"] = $discountid;
        $this->output("discount_oper");
    }

    /**
     *
     * 保存限时折扣活动  
     * 
     */
    public function discountsave()
    {
        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __("discount");
        switch ($opertype) {
            case 'add':
            case 'edit':
                //接收参数
                $subject = trim($_POST["subject"]);
                $begintime = strtotime($_POST["begintime"]);
                $endtime = strtotime($_POST["endtime"]);

                //限时折扣商品
                $discounts = $_POST["discount"];
                $limits = $_POST["limit"];

                $data = array("subject" => $subject, "begintime" => $begintime, "endtime" => $endtime);

                $discountid = intval($_POST["discountid"]);
                if ($discountid) {
                    //更新套餐
                    $ret = DB::getDB()->update("discount", $data, "discountid='$discountid'");

                    //套餐商品
                    $adddata = array();
                    $exists = DB::getDB()->select("discount_item", "itemid", "discountid='$discountid'", null, null, "itemid");
                    if ($discounts) {
                        foreach ($discounts as $key => $discount) {
                            if (cstrpos($key, "key")) { //更新
                                $itemid = intval(trim($key, "key_"));
                                DB::getDB()->update("discount_item", array("discount" => getPrice($discount, 2, 'int'), "limit" => $limits[$key]), "discountid='$discountid' AND itemid='$itemid'");
                                unset($exists[$itemid]);
                            } else { //增加
                                $adddata[] = array("itemid" => $key, "discount" => getPrice($discount, 2, 'int'),
                                    "discountid" => $discountid, "limit" => $limits[$key]);
                            }
                        }
                    }

                    if ($exists)
                        DB::getDB()->delete("discount_item", array("discountid" => $discountid, "itemid" => "in " . cimplode(array_keys($exists))));
                    if ($adddata)
                        DB::getDB()->insertMulti("discount_item", $adddata);
                    $this->adminlog("al_discount", array("do" => "edit", "subject" => $subject));
                    $this->setHint(__("edit_success", $text));
                } else {
                    //新建套餐
                    $discountid = DB::getDB()->insert("discount", $data);
                    //套餐商品
                    if ($discounts) {
                        $adddata = array();
                        foreach ($discounts as $itemid => $discount) {
                            $adddata[] = array("itemid" => $itemid,
                                "discountid" => $discountid,
                                "limit" => intval($limits[$itemid]),
                                "discount" => getPrice($discount, 2, 'int'));
                        }
                        if ($adddata)
                            DB::getDB()->insertMulti("discount_item", $adddata);
                    }
                    $this->adminlog("al_discount", array("do" => "add", "subject" => $subject));
                    $this->setHint(__("add_success", $text));
                }
                break;
            case 'editfield':
                $field = strtolower($_REQUEST["field"]);
                $ret = false;
                if ($field == "delete") {
                    $discountidstr = strtolower($_POST["idstr"]);
                    if ($discountidstr) {
                        $discountids = explode(",", $discountidstr);
                        $where = "discountid in " . cimplode($discountids);

                        $discounts = DB::getDB()->selectkv("discount", "discountid", "subject", $where);
                        foreach ($discounts as $subject) {
                            $this->adminlog("al_discount", array("do" => "del", "subject" => $subject));
                        }

                        DB::getDB()->delete("discount", $where);
                        DB::getDB()->delete("discount_item", $where);
                        exit("success");
                    }
                } else if ($field == "publish") { //设置发布属性
                    $discountid = intval($_GET["discountid"]);
                    $this->adminlog("al_discount", array("do" => "edit", "discountid" => $discountid));

                    $ret = DB::getDB()->updatebool("discount", "ispublish", "discountid='$discountid'");
                    $this->setHint(__('set_success', array($text, __('publish_property'))));
                }
                exit($ret ? "success" : "failure");
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
        $discountid = isset($_GET["discountid"]) ? intval($_GET["discountid"]) : 0;
        //分页
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $pagesize = isset($_REQUEST["pagesize"]) ? intval($_REQUEST["pagesize"]) : 10;

        $where['type'] = 'discount';
        if ($discountid)
            $where["typeid"] = $discountid;

        $onarr = array("on" => "typeid,discountid");
        $count = DB::getDB()->joincount("trade_promotion", "discount", $onarr, array("a" => $where));
        if ($count) {
            //获取分页参数
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            //查询数据
            $this->data["records"] = DB::getDB()->join("trade_promotion", "discount", $onarr, array("a" => "*", "b" => "subject"), array("a" => $where), array("a" => "addtime DESC"), $this->data['pagearr']['limit']);
            $tradeids = array();
            foreach ($data['records'] as $record) {
                $tradeids[] = $record['tradeid'];
            }
            if ($tradeids) {
                $this->data['trades'] = DB::getDB()->selectkv("trade", "tradeid", "status", "tradeid in " . cimplode($tradeids));
            }
        }
        $this->output("discount_order");
    }

}
