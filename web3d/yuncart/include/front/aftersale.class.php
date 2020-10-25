<?php

defined('IN_CART') or die;

/**
 *  
 * 退换货
 *
 *
 * */
class Aftersale extends Base
{

    private $uid;

    /**
     *
     * 构造函数，赋值uid
     *
     */
    public function __construct($model, $action)
    {
        parent::__construct($model, $action);
        if (!isset($_SESSION["uid"])) {
            redirect(url("index", 'user', "login"));
        }
        $this->uid = intval($_SESSION["uid"]);
    }

    /**
     *
     * 订单
     *
     */
    public function trade()
    {
        //参数
        $ordertime = !empty($_GET['ordertime']) ? intval($_GET['ordertime']) : 30;
        ($ordertime != 30) && ($ordertime != 40) && ($ordertime = 30);

        list($page, $pagesize) = $this->getRequestPage();

        $where[] = "uid='" . $this->uid . "' AND (isfinish=1 or status='WAIT_RECE') AND iscancel=0";
        if ($ordertime == 30) { //订单时间
            $where[] = "addtime > " . strtotime("-30 days");
        } else {
            $where[] = "addtime <" . strtotime("-30 days");
        }
        $this->data['ordertime'] = $ordertime;
        $wherestr = implode(" AND ", $where);

        //订单数据
        $count = DB::getDB()->selectcount("trade", $wherestr);
        if ($count) {

            $this->data["pagearr"] = getPageArr($page, $pagesize, $count, url('index', 'aftersale', 'trade', "ordertime=$ordertime"));

            //订单
            $this->data['trades'] = DB::getDB()->select("trade", "tradeid,receiver_name,totalfee,addtime,status", $wherestr, "tradeid DESC", $this->data['pagearr']['limit'], "tradeid");

            $tradeids = array_keys($this->data['trades']);

            //订单商品
            $orders = DB::getDB()->select("order", "itemid,itemimg,itemname,tradeid,orderid,applied", "tradeid in " . cimplode($tradeids));

            foreach ($orders as $order) {
                $this->data['trades'][$order['tradeid']]['order'][$order['orderid']] = $order;
            }
        }
        $this->output("aftersale_trade");
    }

    /**
     *
     * 申请退换货
     *
     */
    public function apply()
    {
        $this->getHint();
        $orderid = intval($_GET["orderid"]);
        //判断该订单
        $this->data['order'] = DB::getDB()->selectrow("order", "*", "orderid='$orderid' AND uid='" . $this->uid . "'");
        if (!$this->data['order']) {//订单不存在
            $this->output("aftersale_apply");
        }
        if ($this->data['order']['applied']) {
            $this->output("aftersale_apply");
        }
        $tradeid = $this->data['order']['tradeid'];
        $this->data['trade'] = DB::getDB()->selectrow("trade", "*", "tradeid='$tradeid'");
        if (!$this->data['trade'] || $this->data['trade']['iscancel'] || (!$this->data['trade']['isfinish'] && $this->data['trade']['status'] != 'WAIT_RECE')) {//订单尚未结束
            $this->output("aftersale_apply");
        }

        //商品赠品
        $this->data['gifts'] = DB::getDB()->selectrow("trade_gift", "*", "orderid='$orderid' AND tradeid='$tradeid'");

        $thetime = $this->data['trade']['isfinish'] ? time() - $this->data['trade']['endtime'] : 0;
        $back = getConfig('aftersale_back', 0);
        $change = getConfig('aftersale_change', 0);
        $repair = getConfig('aftersale_repair', 0);
        $this->data['ways'] = array("back" => $back,
            "canback" => $thetime < $back * 86400,
            "change" => $change,
            "canchange" => $thetime < $change * 86400,
            "repair" => $repair,
            "canrepair" => $thetime < $repair * 86400);

        $this->data['backinfo'] = getConfig('aftersale_backinfo');


        $province = $this->data["trade"]['receiver_province'];
        $city = $this->data["trade"]['receiver_city'];
        $district = $this->data["trade"]['receiver_district'];
        $this->data['provinceopt'] = Dis::getDistrict(0, $province, "option");
        $province && $this->data['cityopt'] = Dis::getDistrict($province, $city, "option");
        $city && $this->data['districtopt'] = Dis::getDistrict($city, $district, "option");

        if ($change && $this->data['order']['productid']) {//如果可以更换，且存在货品,目前不考虑价格不一致的影响
            $this->data['productopt'] = SKU::getProduct($this->data['order']['itemid']);
        }


        $this->output("aftersale_apply");
    }

    /**
     *
     * 提交
     *
     */
    public function applyok()
    {
        $orderid = intval($_POST["orderid"]);
        //如果订单不存在
        $order = DB::getDB()->selectrow("order", "*", "orderid='$orderid' AND uid='" . $this->uid . "'");
        if (!$order) {
            $this->setHint("no_trade_or_trade_not_finish", "error");
        }
        if ($order['applied']) {
            $this->setHint("trade_is_applied", "error");
        }
        //如果订单不存在
        $tradeid = $order['tradeid'];
        $trade = DB::getDB()->selectrow("trade", "*", "tradeid='$tradeid'");
        if (!$trade || $trade['iscancel'] || (!$trade['isfinish'] && $trade['status'] != 'WAIT_RECE')) {
            $this->setHint("no_trade_or_trade_not_finish", "error");
        }

        $problem = strip_tags($_POST["problem"]);
        $way = trim($_POST["way"]);
        $receiver_name = trim($_POST["name"]);
        $receiver_province = trim($_POST["province"]);
        $receiver_city = trim($_POST["city"]);
        $receiver_district = trim($_POST["district"]);
        $receiver_address = trim($_POST["address"]);
        $receiver_zip = trim($_POST["zipcode"]);
        $receiver_link = trim($_POST["link"]);
        $chgproductid = isset($_POST["productid"]) ? intval($_POST["productid"]) : 0;
        $chgspec = $chgproductid ? implode(",", SKU::getProductSpecs($order['itemid'], $chgproductid)) : '';
        //入库
        DB::getDB()->insert("aftersale", array("problem" => $problem,
            "way" => $way,
            "receiver_name" => $receiver_name,
            "receiver_province" => $receiver_province,
            "receiver_city" => $receiver_city,
            "receiver_district" => $receiver_district,
            "receiver_address" => $receiver_address,
            "receiver_zip" => $receiver_zip,
            "receiver_link" => $receiver_link,
            "tradeid" => $tradeid,
            "orderid" => $orderid,
            "uid" => $this->uid,
            "uname" => $_SESSION['uname'],
            "chgproductid" => $chgproductid,
            "chgspec" => $chgspec,
            "addtime" => time()));
        //更新订单为申请状态
        DB::getDB()->update("order", "applied=1", "orderid='$orderid' AND uid='" . $this->uid . "'");

        $this->setHint("aftersale_apply_success", 'success');
    }

    /**
     *
     * 查看我的申请
     *
     */
    public function my()
    {
        list($page, $pagesize) = $this->getRequestPage();
        $onarr = array("on" => "orderid");
        $where = array("a" => "uid='" . $this->uid . "'", "b" => "isdel=0");
        $count = DB::getDB()->joincount("aftersale", "order", $onarr, $where);
        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count, url("index", "aftersale", "my"));
            //查询数据
            $this->data["afterlist"] = DB::getDB()->join("aftersale", "order", $onarr, array("a" => "*", "b" => "itemid,itemname,itemimg,productid"), $where, array("a" => "afterid DESC"), $this->data['pagearr']['limit']);
        }

        $this->output("myaftersale");
    }

}
