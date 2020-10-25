<?php

defined('IN_CART') or die;

/**
 *
 * 搭配套餐 
 * 
 */
class Meal extends Base
{

    /**
     *
     * ispublish 1 有效，无效 
     * 
     */
    public function index()
    {
        list($page, $pagesize) = $this->getRequestPage();
        $count = DB::getDB()->selectcount("meal");
        if ($count) {
            //获取分页参数
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);

            //查询数据
            $this->data["meals"] = DB::getDB()->select("meal", "*", "", "mealid DESC", $this->data['pagearr']['limit'], "mealid");
        }

        $this->output("meal_index");
    }

    /**
     *
     * 增加一个套餐 
     * 
     */
    public function mealadd()
    {
        $this->data["opertype"] = "add";
        $this->data['leftcur'] = "meal_index";
        $this->output("meal_oper");
    }

    /**
     *
     * 修改一个套餐 
     * 
     */
    public function mealedit()
    {
        $mealid = intval($_GET["mealid"]);
        $where = "mealid='$mealid'";
        $this->data["mealid"] = $mealid;
        $this->data["opertype"] = "edit";
        $this->data['meal'] = DB::getDB()->selectrow("meal", "*", $where);
        $this->data["mealitems"] = DB::getDB()->select("meal_item", "*", $where);

        $this->output("meal_oper");
    }

    /**
     *
     * 保存套餐 
     * 
     */
    public function mealsave()
    {
        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __('meal');
        switch ($opertype) {
            case 'add':
            case 'edit':
                $title = trim($_POST["title"]);
                $price = getPrice($_POST["price"], 2, 'int');
                $oldprice = getPrice($_POST['oldprice'], 2, 'int');
                $desc = trim($_POST["desc"]);
                $order = intval($_POST['order']);
                $inventory = intval($_POST['inventory']);
                $begintime = trim($_POST["begintime"]);
                $endtime = trim($_POST["endtime"]);


                $itemids = $_POST["itemid"];
                $useitemids = array();
                foreach ($itemids as $v) {
                    $useitemids[] = cstrpos($v, "key") ? intval(trim($v, "key_")) : $v;
                }
                $items = DB::getDB()->select("item", "itemid,itemname,itemimg,price", "itemid in " . cimplode($useitemids), "", "", "itemid");
                $topitemid = current($useitemids);
                $topitem = $items[$topitemid];

                $data = array("title" => $title,
                    "price" => $price,
                    "oldprice" => $oldprice,
                    "desc" => $desc,
                    "order" => $order,
                    "itemid" => $topitem['itemid'],
                    "itemname" => $topitem["itemname"],
                    "itemimg" => $topitem['itemimg'],
                    "inventory" => $inventory,
                    "begintime" => strtotime($begintime),
                    "endtime" => strtotime($endtime));

                $mealid = intval($_POST["mealid"]);
                if ($mealid) {
                    //更新套餐
                    $ret = DB::getDB()->update("meal", $data, "mealid='$mealid'");

                    //套餐商品
                    $dbexists = DB::getDB()->select("meal_item", "itemid", "mealid='$mealid'", null, null, "itemid");
                    $order = 0;
                    $adddata = array();
                    if ($itemids) {
                        foreach ($itemids as $itemid) {
                            $order ++;
                            if (cstrpos($itemid, "key")) { //修改
                                $itemid = intval(trim($itemid, "key_"));
                                DB::getDB()->update("meal_item", array("order" => $order,
                                    "itemname" => $items[$itemid]['itemname'],
                                    "itemimg" => $items[$itemid]["itemimg"],
                                    "price" => $items[$itemid]["price"]), "mealid='$mealid' AND itemid='$itemid'");
                                unset($dbexists[$itemid]);
                            } else { //增加
                                $adddata[] = array(
                                    "itemid" => $itemid,
                                    "mealid" => $mealid,
                                    "itemname" => $items[$itemid]["itemname"],
                                    "itemimg" => $items[$itemid]["itemimg"],
                                    "order" => $order,
                                    "price" => $items[$itemid]["price"]
                                );
                            }
                        }
                    }
                    if ($adddata)
                        DB::getDB()->insertMulti("meal_item", $adddata);
                    if ($dbexists)
                        DB::getDB()->delete("meal_item", "mealid='$mealid' AND itemid in " . cimplode(array_keys($dbexists)));

                    $this->adminlog("al_meal", array("do" => "edit", "title" => $title));
                    $this->setHint(__("edit_success", $text));
                } else {
                    //新建套餐
                    $data["ispublish"] = 1;
                    $mealid = DB::getDB()->insert("meal", $data);

                    //套餐商品
                    if ($itemids) {
                        $adddata = array();
                        $order = 1;
                        foreach ($itemids as $key => $itemid) {
                            $adddata[] = array(
                                "itemid" => $itemid,
                                "mealid" => $mealid,
                                "itemname" => $items[$itemid]["itemname"],
                                "itemimg" => $items[$itemid]["itemimg"],
                                "order" => $order++,
                                "price" => $items[$itemid]["price"]
                            );
                        }
                        if ($adddata)
                            DB::getDB()->insertMulti("meal_item", $adddata);
                    }
                    $this->adminlog("al_meal", array("do" => "add", "title" => $title));
                    $this->setHint(__("add_success", $text));
                }
                break;
            case 'editfield':
                $field = strtolower($_REQUEST["field"]);
                $ret = false;
                if ($field == "delete") {
                    $mealidstr = trim($_POST["idstr"]);
                    if ($mealidstr) {
                        $mealids = explode(",", $mealidstr);
                        $where = "mealid in " . cimplode($mealids);

                        $meals = DB::getDB()->selectkv("meal", "mealid", "title", $where);
                        foreach ($meals as $title) {
                            $this->adminlog("al_meal", array("do" => "edit", "title" => $title));
                        }

                        DB::getDB()->delete("meal", $where);
                        DB::getDB()->delete("meal_item", $where);
                        exit("success");
                    }
                } else if ($field == "publish") {
                    $mealid = intval($_GET["mealid"]);
                    $ret = DB::getDB()->updatebool("meal", "ispublish", "mealid='$mealid'");
                    $this->adminlog("al_meal", array("do" => "edit", "mealid" => $mealid));
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
        $mealid = isset($_GET["mealid"]) ? intval($_GET["mealid"]) : 0;
        //分页
        list($page, $pagesize) = $this->getRequestPage();

        $where['type'] = 'meal';
        if ($mealid)
            $where["typeid"] = $mealid;

        $onarr = array("on" => "typeid,mealid");
        $count = DB::getDB()->joincount("trade_promotion", "meal", $onarr, array("a" => $where));
        if ($count) {
            //获取分页参数
            $data["pagearr"] = getPageArr($page, $pagesize, $count);
            //查询数据
            $data["records"] = DB::getDB()->join("trade_promotion", "meal", $onarr, array("a" => "*", "b" => "title"), array("a" => $where), array("a" => "addtime DESC"), $this->data['pagearr']['limit']);
            $tradeids = array();
            foreach ($data['records'] as $record) {
                $tradeids[] = $record['tradeid'];
            }
            if ($tradeids) {
                $this->data['trades'] = DB::getDB()->selectkv("trade", "tradeid", "status", "tradeid in " . cimplode($tradeids));
            }
        }
        $this->output("meal_order");
    }

}
