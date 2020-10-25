<?php

defined('IN_CART') or die;

/**
 *
 * 购买
 *
 */
class Buy extends Base
{

    private $uid;

    /**
     *
     * 赋值uid
     *
     */
    public function __construct($model, $action)
    {
        parent::__construct($model, $action);
        if (empty($_SESSION["uid"])) {
            redirect(url("index", 'user', "login"));
        }
        $this->uid = intval($_SESSION["uid"]);
    }

    /**
     *
     * 购买，输入用户信息
     *
     */
    public function index()
    {
        //订单信息
        $carts = !empty($_SESSION["cart"]["list"]) ? $_SESSION["cart"]["list"] : array();
        if (!$carts)
            cerror(__("empty_cart"));

        $this->data['cartitems'] = $this->getCartItems();
        //满减活动
        $this->data['man'] = Promotion::getManRule($_SESSION['cart']['itemfee']);


        //支付方式
        $this->data["payments"] = DB::getDB()->select("payment", "name,memo,paymentid", "ispublish=1", "order");

        //优惠券
        $this->data['coupons'] = Promotion::getCoupons($this->uid);

        $this->output("buy");
    }

    /**
     *
     * 获取地址
     *
     */
    public function operaddr($addressid = 0)
    {
        $opertype = $addressid ? "use" : trim($_REQUEST["opertype"]);

        $where = "uid='" . $this->uid . "'";
        if ($opertype == "use") {//使用这个地址
            !$addressid && ($addressid = intval($_POST["addressid"]));
            $this->data['address'] = DB::getDB()->selectrow("user_address", "*", "addressid='$addressid' AND $where");
            $this->data['address']['address'] = Dis::getText($this->data['address']['province'], $this->data['address']['city'], $this->data['address']['district']) . $this->data['address']['address'];

            $this->data['waylist'] = $this->getExpressWay($this->data['address']['province']);
        } elseif ($opertype == "get") {//获取地址
            $this->data["addresslist"] = DB::getDB()->select("user_address", "*", $where);
        } elseif ($opertype == "add") {//增加地址
            $this->data["addresslist"] = DB::getDB()->select("user_address", "*", $where);
        } elseif ($opertype == "edit") {//编辑地址
            $addressid = intval($_POST["addressid"]);
            $this->data['uaddress'] = DB::getDB()->selectrow("user_address", "*", "addressid='$addressid' AND $where");
            $this->data["addresslist"] = DB::getDB()->select("user_address", "*", $where);
            $this->data["addressid"] = $addressid;

            $this->getDistrictopt($this->data['uaddress']['province'], $this->data['uaddress']['city'], $this->data['uaddress']['district']);
        } elseif ($opertype == "del") {//删除地址
            $addressid = intval($_POST["addressid"]);
            DB::getDB()->delete("user_address", "addressid='$addressid'");
            exit("success");
        }
        $this->data["opertype"] = $opertype;
        $this->output("buy_address");
    }

    /**
     *  
     * 获取物流方式
     *
     *
     * */
    private function getExpressWay($province)
    {
        $waylist = DB::getDB()->select("express_way", "*", "isdel=0 AND status=1", "order", "", "wayid");
        $twice = array();
        foreach ($waylist as $key => $val) {//循环ship，找出有哪些需要进一步判断
            if ($val['feetype'] == 'self')
                $twice[$val['wayid']] = $val['wayid'];
        }
        if ($twice) {//判断
            $tmp = DB::getDB()->select("express_prov", "wayid,province,price", "wayid in " . cimplode($twice));
            foreach ($tmp as $k => $v) {
                $str = preg_replace("/\|[\x7f-\xff]+/sim", "", $v['province']);
                if (cstrpos($str, $province)) {//如果含有该省
                    $waylist[$v['wayid']]['price'] = $v['price'];
                    unset($twice[$v['wayid']]);
                    if (!$twice)
                        return $waylist;
                }
            }
        }
        foreach ($waylist as $key => $val) {
            if (in_array($key, $twice) && !$val['price']) {//进一步判断后，不符合条件的删除
                unset($waylist[$key]);
            }
        }
        return $waylist;
    }

    /**
     *  
     * 保存用户地址
     *
     *
     * */
    public function saveaddr()
    {
        $receiver = trim($_POST["receiver"]);
        $province = trim($_POST["province"]);
        $city = trim($_POST["city"]);
        $district = trim($_POST["district"]);
        $zipcode = trim($_POST["zipcode"]);
        $link = trim($_POST["link"]);
        $address = trim($_POST["address"]);
        $addressid = intval($_POST["addressid"]);
        $data = array(
            "receiver" => $receiver,
            "province" => $province,
            "city" => $city,
            "district" => $district,
            "zipcode" => $zipcode,
            "link" => $link,
            "address" => $address,
            "uid" => $this->uid);
        if ($addressid && DB::getDB()->selectexist("user_address", "addressid", "addressid='$addressid' AND uid='" . $this->uid . "'")) { //更新
            DB::getDB()->update("user_address", $data, "addressid='$addressid'");
        } else {
            $addressid = DB::getDB()->insert("user_address", $data);
        }
        $this->operaddr($addressid);
    }

    /**
     *  
     * 提交订单
     *
     *
     * */
    public function ordercomp()
    {

        $time = time();
        //判断订单执行条件
        //购物车
        $carts = $_SESSION["cart"]["list"];
        if (!$carts) {
            redirect(array("index", "cart"));
        }
        //收货地址
        $addressid = intval($_POST["addressid"]);
        $address = DB::getDB()->selectrow("user_address", "*", "uid=" . $this->uid . " AND addressid='$addressid'");
        if (!$address)
            cerror(__("error_address"));

        //支付方式
        $paymentid = intval($_POST["paymentid"]);
        $payment = DB::getDB()->selectrow("payment", "code,paymentid", "ispublish=1 AND paymentid='$paymentid'");
        if (!$payment)
            cerror(__("cannt_user_thepayment"));
        $paymentcode = $payment['code'];

        //快递
        $wayid = intval($_POST["wayid"]);
        $way = DB::getDB()->selectrow("express_way", "*", "wayid='$wayid'");
        if (!$way)
            cerror(__("error_expressway"));

        $man = Promotion::getManRule($_SESSION['cart']['itemfee'], true);
        $postfee = 0;
        if (!$man || !empty($man['nofreight'])) { //订单免运费
            //第一级，自定义省
            $postfee = DB::getDB()->selectval("express_prov", "price", "wayid='$wayid' AND province like '%{$address['province']}%'");
            //第二级，默认
            if (!$postfee && $way['price'])
                $postfee = $way['price'];
            //两级都不存在，报错
            !$postfee && cerror(__("error_expressway"));
        }


        $postfee = $postfee ? getPrice($postfee, -2, 'float') : 0;
        $posttype = intval($_POST["posttype"]);



        //接受其他参数
        $memo = trim($_POST["memo"]);
        $istax = isset($_POST["istax"]) ? 1 : 0;
        $tax_company = $istax ? trim($_POST["tax_company"]) : "";

        //订单商品
        $cartitems = $this->getCartItems();

        //套餐
        $mealid = $mealnum = 0;
        if (!empty($_SESSION['cart']['meal'])) { //如果是套餐订单，扯分
            $temp = array();
            foreach ($cartitems as $k => $cartitem) {
                if ($cartitem['type'] == "meal") { //如果是套餐
                    $mealnum = $cartitem['num'];
                    $mealid = $cartitem['mealid'];

                    //判断套餐库存
                    $inventory = DB::getDB()->selectval("meal", "inventory", "mealid='$mealid'");
                    if (!$inventory || $mealnum > $inventory) {
                        cerror(__('inventory_not_enough'));
                    }

                    foreach ($cartitem['mealitems'] as $mealitem) {
                        $mealitem['num'] = $cartitem['num'];
                        $mealitem['mealid'] = $cartitem['mealid'];
                        $mealitem['mealtitle'] = $cartitem['title'];
                        $temp[] = $mealitem;
                    }
                    break;
                }
            }
            $cartitems = $temp;
        }

        //商品bn，检查库存是否满足
        $itemids = $productids = array();
        foreach ($cartitems as $cartitem) {
            $itemids[] = $cartitem['itemid'];
            isset($cartitem['productid']) && ($productids[] = $cartitem['productid']);
        }

        $items = DB::getDB()->select("item", "inventory,bn,itemid", "itemid in " . cimplode($itemids), "", "", "itemid");
        $products = DB::getDB()->select("product", "inventory,bn,productid", "productid in " . cimplode($productids), "", "", "productid");

        $items[0]['bn'] = $products[0]['bn'] = '';
        foreach ($cartitems as $k => $cartitem) {//检查库存
            $itemid = $cartitem['itemid'];
            $productid = !empty($cartitem['productid']) ? $cartitem['productid'] : 0;
            $num = $cartitem['num'];
            if (!$num) { //如果数量不存在，直接unset
                unset($cartitems[$k]);
            } else if (($productid && $num > $products[$productid]['inventory'] ) || ($itemid && $num > $items[$itemid]['inventory'])) {
                //如果productid存在，库存小
                cerror(__('inventory_not_enough'));
            }
        }

        //订单数据 trade
        $itemfee = $_SESSION['cart']['itemfee'];
        $totalfee = $itemfee //商品的费用
                + ($man && !empty($man['nofreight']) ? 0 : $postfee)  //包邮
                - ($man && !empty($man['minus']) ? $man['minus'] : 0)  //减金额
        ;

        //优惠券
        $couponid = isset($_POST["couponid"]) ? $_POST["couponid"] : 0;
        $coupon = array();
        if ($couponid) {//判断优惠券是否存在
            $coupon = Promotion::getCoupon($this->uid, $couponid);
            if ($coupon && $coupon['deno'] && ($coupon['require'] <= $totalfee)) { //优惠券
                $totalfee -= $coupon['deno'];
            } else { //优惠券不可用
                cerror(__("coupon_cannot_use"));
            }
        }
        $totalfee < 0 && $totalfee = 0;

        //正式进入执行订单流程
        if (isset($_SESSION['cart']['in'])) { //重复执行订单
            unset($_SESSION['cart']);
            cerror(__("submit_order_twice"));
        }
        $_SESSION['cart']['in'] = true; //正在执行订单

        $tradeid = $time . mt_rand(100, 999);
        $data = array(
            "tradeid" => $tradeid,
            "uid" => $this->uid,
            "uname" => $_SESSION['uname'],
            "addtime" => $time,
            "status" => "WAIT_PAY", //未支付
            "totalfee" => getPrice($totalfee, 2, 'int'),
            "itemfee" => getPrice($itemfee, 2, 'int'),
            "postfee" => getPrice($postfee, 2, 'int'),
            "man" => $man ? $man['str'] : '',
            "coupon" => $coupon ? $coupon['deno'] : 0,
            "expresswayid" => $wayid,
            "posttype" => $posttype,
            "receiver_name" => $address['receiver'],
            "receiver_province" => $address['province'],
            "receiver_city" => $address['city'],
            "receiver_district" => $address['district'],
            "receiver_address" => $address['address'],
            "receiver_zip" => $address['zipcode'],
            "receiver_link" => $address['link'],
            "memo" => $memo,
            "payment" => $paymentcode,
            "istax" => $istax,
            "tax_company" => $tax_company
        );
        DB::getDB()->insert("trade", $data);
        $adddata = $promodata = array();

        //订单
        foreach ($cartitems as $cartitem) { //组装order表数据
            $itemid = $cartitem['itemid'];
            $productid = !empty($cartitem['productid']) ? $cartitem['productid'] : 0;
            $num = $cartitem['num'];

            $adddata = array("tradeid" => $tradeid,
                "ibn" => $items[$itemid]['bn'],
                "pbn" => $products[$productid]['bn'],
                "itemid" => $itemid,
                "productid" => $productid,
                "itemname" => $cartitem['itemname'],
                "itemimg" => $cartitem['itemimg'],
                "num" => $num,
                "price" => getPrice($cartitem['price'], 2, 'int'),
                "uid" => $this->uid,
                "uname" => $_SESSION['uname'],
                "addtime" => $time
            );
            //搭配套餐
            if (isset($cartitem['mealid'])) {
                $adddata["mealtitle"] = $cartitem["mealtitle"];
            }
            //限时打折
            if (isset($cartitem['discount'])) {
                $adddata['discount'] = getPrice($cartitem['discount']['oldprice'] - $cartitem['discount']['newprice'], 2, 'int');
            } else if (isset($cartitem['tuan'])) {
                $adddata['tuan'] = getPrice($cartitem['tuan']['oldprice'] - $cartitem['tuan']['newprice'], 2, 'int');
            }


            //插入订单表
            $orderid = DB::getDB()->insert("order", $adddata);

            if (!empty($cartitem['gifts'])) {//赠品
                $giftdata = array("tradeid" => $tradeid,
                    "orderid" => $orderid,
                    "itemid" => $itemid,
                    "productid" => $productid,
                    "gift" => serialize($cartitem['gifts']));
                DB::getDB()->insert("trade_gift", $giftdata);
            }

            //减去product库存
            if ($productid) {
                DB::getDB()->updatecre("product", "inventory", "productid='$productid'", 'decre', $num);
            }
            //减去item库存
            DB::getDB()->updatecremulti("item", array("inventory" => "- $num", "modified" => $time), "itemid='$itemid'");

            //tuan buynum
            if (isset($cartitem['tuan'])) {
                DB::getDB()->updatecremulti("tuan", array("buynum" => "+ $num"), "tuanid='" . $cartitem["tuan"]["tuanid"] . "'");
            }
        }



        //更新优惠券已经使用及相关订单
        if ($coupon) {
            DB::getDB()->update("user_coupon", "isused=1,tradeid='$tradeid'", "uid='" . $this->uid . "' AND couponid='" . $coupon['couponid'] . "'");
        }

        //如果是套餐订单，更新套餐的库存和volume
        if ($mealid && $mealnum) {
            DB::getDB()->updatecremulti("meal", array("inventory" => "- $mealnum", "volume" => "+ $mealnum"), "mealid='$mealid'");
        }
        unset($_SESSION["cart"]);

        $this->data['tradeid'] = $tradeid;
        $this->data['total'] = $totalfee;
        $this->data['paymentcode'] = $paymentcode;
        //发送消息通知
        $mq = new MQ("tradecreated");
        $mq->send($this->uid, array(
            "mobile" => $address["link"],
            "replacement" => array($tradeid, getPrice($totalfee, 0))
        ));

        //订单处理结束
        $this->output("ordercomp");
    }

}
