<?php

defined('IN_CART') or die;

/**
 *
 * 我的订单列表 
 *
 */
class Mytrade extends Base
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
        $this->uid = $_SESSION['uid'];
    }

    /**
     *
     * 首页，显示未完成的订单 
     *
     */
    public function index()
    {
        $this->output("mytrade");
    }

    /**
     *
     * 获取订单 
     *
     */
    public function getTrade()
    {
        //参数
        $type = !empty($_GET['type']) ? trim($_GET["type"]) : "";
        !in_array($type, array('run', 'finish', 'cancel')) && $type = 'run';
        $ordertime = !empty($_GET['ordertime']) ? intval($_GET['ordertime']) : 30;
        ($ordertime != 30) && ($ordertime != 40) && ($ordertime = 30);

        $this->data['type'] = $type;
        list($page, $pagesize) = $this->getRequestPage();

        $where[] = "uid='" . $this->uid . "'";
        if ($type == "run") { //正在进行
            $where[] = "isfinish = 0";
        } elseif ($type == "finish") { //已经完成
            $where[] = "isfinish = 1 AND iscancel = 0";
        } elseif ($type == "cancel") { //已经取消
            $where[] = "iscancel = 1";
        }
        if ($ordertime == 30) { //订单时间
            $where[] = "addtime > " . strtotime("-30 days");
        } else {
            $where[] = "addtime <" . strtotime("-30 days");
        }
        $wherestr = implode(" AND ", $where);

        //订单数据
        $count = DB::getDB()->selectcount("trade", $wherestr);
        if ($count) {

            $this->data["pagearr"] = getPageArr($page, $pagesize, $count, '', true);

            //订单
            $this->data['trades'] = DB::getDB()->select("trade", "tradeid,receiver_name,totalfee,addtime,status,payment", $wherestr, "tradeid DESC", $this->data['pagearr']['limit'], "tradeid");

            $tradeids = array_keys($this->data['trades']);

            //订单商品
            $orders = DB::getDB()->select("order", "itemid,itemimg,itemname,tradeid,orderid", "tradeid in " . cimplode($tradeids));

            foreach ($orders as $order) {
                $this->data['trades'][$order['tradeid']]['order'][$order['orderid']] = $order;
            }
        }
        $this->output("gettrade");
    }

    /**
     *
     * 取消订单 
     *
     */
    public function cancel()
    {
        $tradeid = trim($_POST["tradeid"]);
        $trade = DB::getDB()->selectrow("trade", "*", "tradeid='$tradeid' AND uid='" . $this->uid . "'");
        if ($trade && $trade['status'] == "WAIT_PAY") { //订单存在，且未支付，可取消
            DB::getDB()->update("trade", array("status" => 'CANCELED', "iscancel" => 1, "isfinish" => 1), "tradeid='$tradeid'");
            if ($trade['coupon']) {//如果使用了优惠券
                DB::getDB()->update("user_coupon", "tradeid='',isused=0", "isused=1 AND tradeid='$tradeid'");
            }
            //发消息
            $mq = new MQ("tradeclose");
            $mq->send($trade['uid'], array(
                "mobile" => $trade['receiver_link'],
                "replacement" => array($tradeid)));
            exit("success");
        }
        exit('failure');
    }

    /**
     *
     * 查看订单 
     *
     */
    public function view()
    {
        $tradeid = trim($_GET["tradeid"]);
        //判断用户是否有
        $where = "tradeid='$tradeid' AND uid = '" . $this->uid . "'";
        $this->data['trade'] = DB::getDB()->selectrow("trade", "*", $where);
        if ($this->data['trade']) {
            $this->data['orders'] = DB::getDB()->select("order", "*", $where);
            $this->data['send'] = DB::getDB()->selectrow("trade_send", "*", "tradeid='$tradeid'");
            if ($this->data['send']) {
                $this->data['company'] = DB::getDB()->selectrow("express_company", "*", "companyid='" . $this->data['send']['companyid'] . "'");
                $this->data['kuaidi_status'] = getConfig("kuaidi_status");
            }
            $gifts = DB::getDB()->select("trade_gift", "*", "tradeid='$tradeid'");
            $this->data['way'] = DB::getDB()->selectval("express_way", "name", "wayid='" . $this->data['trade']['expresswayid'] . "'");
            $this->data["gifts"] = array();
            foreach ($gifts as $gift) {
                $this->data["gifts"][$gift['orderid']] = $gift;
            }
        }

        $this->output("viewtrade");
    }

    public function trace()
    {
        $kuaidi_status = getConfig("kuaidi_status");
        if ($kuaidi_status) {
            $nu = $_GET["nu"];
            $com = $_GET["com"];
            $key = getConfig("kuaidi_key");
            if ($nu && $com && $key) {
                $this->data["nu"] = $nu;
                $this->data["com"] = $com;
                $url = "http://api.kuaidi100.com/api?id=$key&com=$com&nu=$nu&order=asc";
                $this->data['content'] = @json_decode(file_get_contents($url), true);
            }
        }
        $this->output("kuaidi_trace");
    }

}
