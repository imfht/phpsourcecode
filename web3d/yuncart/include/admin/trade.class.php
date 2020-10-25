<?php

defined('IN_CART') or die;

/**
 *
 * 订单列表 
 *
 */
class Trade extends Base
{

    /**
     *  
     * 列出所有订单
     *
     *
     * */
    public function index()
    {
        //分页
        list($page, $pagesize) = $this->getRequestPage();

        $where[] = "isdel = 0";
        //搜索
        $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : "";
        $time1 = isset($_REQUEST['time1']) ? strtotime(trim($_REQUEST['time1'])) : "";
        $time2 = isset($_REQUEST['time2']) ? strtotime(trim($_REQUEST['time2'])) : "";
        $qtype = isset($_REQUEST['qtype']) ? trim($_REQUEST['qtype']) : "item";
        $do = isset($_REQUEST['do']) ? trim($_REQUEST['do']) : "";
        //订单时间
        $ordertime = 0;
        if (!$time1 && !$time2) {
            $ordertime = !empty($_REQUEST['ordertime']) ? intval($_REQUEST['ordertime']) : 30;
        }

        $this->data += array('q' => $q, "qtype" => $qtype, "time1" => $time1, "time2" => $time2, "ordertime" => $ordertime);
        if ($q) {
            if ($qtype == 'receiver') { //收货人
                $where[] = "receiver_name like '%" . $q . "%'";
            } elseif ($qtype == 'user') { //会员
                if (is_numeric($q)) {
                    $where[] = "uid='" . $q . "'";
                } else {
                    $where[] = "uname like '%" . $q . "%'";
                }
            } elseif ($qtype == "trade") { //订单号
                $where[] = "tradeid='" . $q . "'";
            }
        }
        //订单状态
        $type = isset($_REQUEST['type']) ? strtolower(trim($_REQUEST['type'])) : 'all';
        !in_array($type, array('all', 'wait_pay', "wait_send", 'wait_rece', 'finish', 'cancel')) && ($type = 'all');
        $this->data['type'] = $type;
        if ($type == "wait_pay") { //正在进行
            $where[] = "status='WAIT_PAY'";
        } elseif ($type == "wait_send") {
            $where[] = "status = 'WAIT_SEND'";
        } elseif ($type == "wait_rece") {
            $where[] = "status = 'WAIT_RECE'";
        } elseif ($type == "finish") { //已经完成
            $where[] = "status = 'FINISH'";
        } elseif ($type == "cancel") { //已经取消
            $where[] = "status = 'CANCELED'";
        }
        $time1 && $where[] = "addtime > '$time1'";
        $time2 && $where[] = "addtime < '$time2'";

        if ($ordertime == 30) { //订单时间
            $where[] = "addtime > " . strtotime("-30 days");
        } else if ($ordertime == 40) {
            $where[] = "addtime < " . strtotime("-30 days");
        }
        $wherestr = implode(' AND ', $where);

        $this->data['kuaidi_status'] = getConfig("kuaidi_status");

        if ($do == "import") {
            $this->_tradeimport($wherestr);
        } else {
            $count = DB::getDB()->selectcount("trade", $wherestr);
            if ($count) {
                //获取分页参数
                $this->data["pagearr"] = getPageArr($page, $pagesize, $count);

                //查询数据
                $this->data['trades'] = DB::getDB()->select("trade", "*", $wherestr, "tradeid DESC", $this->data['pagearr']['limit'], "tradeid");


                //订单商品
                $tradeids = array_keys($this->data['trades']);
                $orders = DB::getDB()->select("order", "*", "tradeid in " . cimplode($tradeids));

                foreach ($orders as $order) {
                    $this->data['trades'][$order['tradeid']]['order'][$order['orderid']] = $order;
                }
            }
            $this->output("trade_index");
        }
    }

    /**
     *  
     * 导出订单
     *
     *
     * */
    private function _tradeimport($wherestr)
    {
        $count = DB::getDB()->selectcount("trade", $wherestr);
        if (!$count)
            $this->setHint(__("no_data_import"));

        $trades = DB::getDB()->select("trade", "*", $wherestr, "tradeid DESC", null, "tradeid");
        $content = __("tradeid") . ","
                . __("receiver_name") . ","
                . __("receiver_address") . ","
                . __("receiver_link") . ","
                . __("trade_status") . ","
                . __("itemfee") . ","
                . __("postfee") . ","
                . __("trade_memo") . ","
                . __("tax") . ","
                . __("tax_company") . ","
                . __("orderbegin") . ","
                . __("orderend")
                . CRLF;
        foreach ($trades as $trade) {
            $content .= $trade['tradeid'] . ","
                    . $trade['receiver_name'] . ","
                    . getDistrict($trade['receiver_province'], $trade['receiver_city'], $trade['receiver_district']) . $trade['receiver_address'] . ","
                    . $trade['receiver_link'] . ","
                    . getCommonCache($trade['status'], "tradestatus") . ","
                    . getPrice($trade['itemfee']) . ","
                    . getPrice($trade['postfee']) . ","
                    . $trade['memo'] . ","
                    . ($trade['istax'] ? __("yes") : __("no")) . ","
                    . $trade['tax_company'] . ","
                    . date("m-d", $trade['addtime']) . ","
                    . ($trade['endtime'] ? date("m-d", $trade['endtime']) : "")
                    . CRLF;
        }
        import($content);
    }

    /**
     *  
     * 列出所有订单商品
     *
     *
     * */
    public function item()
    {
        //分页
        list($page, $pagesize) = $this->getRequestPage();


        //搜索
        $where = array();
        $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : "";
        $qtype = isset($_REQUEST['qtype']) ? trim($_REQUEST['qtype']) : "item";
        $time1 = isset($_REQUEST['time1']) ? strtotime(trim($_REQUEST['time1'])) : "";
        $time2 = isset($_REQUEST['time2']) ? strtotime(trim($_REQUEST['time2'])) : "";
        $do = isset($_REQUEST['do']) ? trim($_REQUEST['do']) : "";

        //订单时间
        $ordertime = 0;
        if (!$time1 && !$time2) {
            $ordertime = !empty($_REQUEST['ordertime']) ? intval($_REQUEST['ordertime']) : 30;
        }
        $this->data += array('q' => $q, "qtype" => $qtype, "time1" => $time1, "time2" => $time2, "ordertime" => $ordertime);

        if ($q) {
            if ($qtype == "trade") { //订单号
                $where['b'][] = "b.tradeid='" . $q . "'";
            } elseif ($qtype == "item") {
                if (is_numeric($q)) {
                    $where['a'][] = "a.itemid = '" . $q . "'";
                } else {
                    $where['a'][] = "a.itemname like '%" . $q . "%'";
                }
            }
        }

        //订单状态
        $type = isset($_REQUEST['type']) ? strtolower(trim($_REQUEST['type'])) : 'all';
        !in_array($type, array('all', 'wait_pay', "wait_send", 'wait_rece', 'finish', 'cancel')) && ($type = 'all');
        $this->data['type'] = $type;
        if ($type == "wait_pay") { //正在进行
            $where['b'][] = "b.status='WAIT_PAY'";
        } elseif ($type == "wait_send") {
            $where['b'][] = "b.status = 'WAIT_SEND'";
        } elseif ($type == "wait_rece") {
            $where['b'][] = "b.status = 'WAIT_RECE'";
        } elseif ($type == "finish") { //已经完成
            $where['b'][] = "b.status = 'FINISH'";
        } elseif ($type == "cancel") { //已经取消
            $where['b'][] = "b.status = 'CANCELED'";
        }

        //订单时间
        $time1 && $where['b'][] = "b.addtime > '$time1'";
        $time2 && $where['b'][] = "b.addtime < '$time2'";
        if ($ordertime == 30) { //订单时间
            $where['b'][] = "b.addtime > " . strtotime("-30 days");
        } else if ($ordertime == 40) {
            $where['b'][] = "b.addtime < " . strtotime("-30 days");
        }


        $wherestr = '1=1';
        !empty($where['b']) && ($wherestr .= " AND " . implode(' AND ', $where['b']));
        !empty($where['a']) && ($wherestr .= " AND " . implode(' AND ', $where['a']));
        //连接

        $joinpara = array("on" => "tradeid");

        if ($do == "import") {
            $this->_itemimport($joinpara, $wherestr);
        } else {
            $count = DB::getDB()->joincount("order", "trade", $joinpara, $wherestr);
            if ($count) {
                $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
                $this->data['tradeitems'] = DB::getDB()->join("order", "trade", $joinpara, array("a" => "*", "b" => "*"), $wherestr, array("a" => "addtime DESC"), $this->data['pagearr']['limit']);
            }
            $this->output("trade_item");
        }
    }

    /**
     *  
     * 导出订单商品
     *
     *
     * */
    private function _itemimport($joinpara, $wherestr)
    {
        $count = DB::getDB()->joincount("order", "trade", $joinpara, $wherestr);
        if (!$count)
            $this->setHint(__("no_data_import"));

        $tradeitems = DB::getDB()->join("order", "trade", $joinpara, array("a" => "*", "b" => "*"), $wherestr);
        $crlf = "\r\n";

        $content = __("tradeid") . ","
                . __("item") . ","
                . __("bigpic") . ","
                . __("price") . ","
                . __("order_num") . ","
                . __("uid") . ","
                . __("uname") . ","
                . __("orderbegin") . ","
                . __("orderend")
                . CRLF;

        //",商品,图片,价格,数量,用户id,用户名,下单时间" . $crlf;
        foreach ($tradeitems as $item) {
            $content .= $item['tradeid'] . "\t,"
                    . str_replace(",", " ", $item['itemname']) . ","
                    . $item['itemimg'] . ","
                    . getPrice($item['price']) . "\t,"
                    . $item['num'] . "\t,"
                    . $item['uid'] . "\t,"
                    . str_replace(",", " ", $item['uname']) . "\t,"
                    . date('m-d', $item['addtime']) . ","
                    . ($item['endtime'] ? date("m-d", $item['endtime']) : "")
                    . CRLF;
        }
        import($content);
    }

    /**
     *  
     * 订单操作
     *
     *
     * */
    public function tradeoper()
    {
        $opertype = trim($_REQUEST["opertype"]);
        switch ($opertype) {
            case 'view': //查看订单详情
                $tradeid = strval($_GET["tradeid"]);
                $where = "tradeid='$tradeid'";
                $this->data['trade'] = DB::getDB()->selectrow("trade", "*", $where);
                $this->data['orders'] = DB::getDB()->select("order", "*", $where);
                $this->data['way'] = DB::getDB()->selectval("express_way", "name", "wayid='" . $this->data['trade']['expresswayid'] . "'");
                $this->data['user'] = DB::getDB()->selectrow("user", "*", "uid='" . $this->data['trade']['uid'] . "'");
                $this->data["send"] = DB::getDB()->selectrow("trade_send", "*", $where);
                $gifts = DB::getDB()->select("trade_gift", "*", $where);
                $this->data["gifts"] = array();
                foreach ($gifts as $gift) {
                    $this->data["gifts"][$gift['orderid']] = $gift;
                }
                $this->output("trade_info");
                break;
            case 'pttrade'://打印订单
                $tradeid = strval($_GET['tradeid']);
                $where = "tradeid='$tradeid'";
                $this->data['trade'] = DB::getDB()->selectrow("trade", "*", $where);
                $this->data['orders'] = DB::getDB()->select("order", "*", $where);

                $gifts = DB::getDB()->select("trade_gift", "*", $where);
                $this->data["gifts"] = array();
                foreach ($gifts as $gift) {
                    $this->data["gifts"][$gift['orderid']] = $gift;
                }
                $this->output("trade_pttrade");
                break;
            case 'ptsend'://打印配送单
                $tradeid = strval($_GET['tradeid']);
                $where = "tradeid='$tradeid'";
                $this->data['trade'] = DB::getDB()->selectrow("trade", "*", $where);
                $this->data['orders'] = DB::getDB()->select("order", "*", $where);

                $gifts = DB::getDB()->select("trade_gift", "*", $where);
                $this->data["gifts"] = array();
                foreach ($gifts as $gift) {
                    $this->data["gifts"][$gift['orderid']] = $gift;
                }
                $this->data['way'] = DB::getDB()->selectval("express_way", "name", "wayid='" . $this->data['trade']['expresswayid'] . "'");
                $this->output("trade_ptsend");
                break;
            case 'ptexpress'://打印快递单
                if (ispostreq()) {
                    $tradeid = trim($_POST["tradeid"]);
                    $tplid = intval($_POST["tplid"]);
                    $addrid = intval($_POST["addrid"]);

                    $where = "tplid='$tplid'";
                    $this->data['trade'] = DB::getDB()->selectrow("trade", "*", "tradeid='$tradeid'");
                    $this->data["tpl"] = DB::getDB()->selectrow("express_tpl", "*", $where);
                    $this->data["sender"] = DB::getDB()->selectrow("express_addr", "*", "addrid='$addrid'");

                    //打印选项
                    $printopt = getCommonCache("all", "printopt");
                    $this->data["selprintopt"] = DB::getDB()->select("express_opt", "*", $where, "", "", "code");

                    $this->data["express"] = array();
                    foreach ($this->data["selprintopt"] as $k => $v) {
                        $map = $printopt[$k]["map"];
                        list($table, $field) = explode("|", $map);
                        if (cstrpos($field, "address")) {
                            $addr = "";
                            if ($table == "sender") {
                                $addr = getDistrict($this->data[$table]["province"], $this->data[$table]["city"], $this->data[$table]["district"]);
                            } else if ($table == "receiver") {
                                $addr = getDistrict($this->data[$table]["receiver_province"], $this->data[$table]["receiver_city"], $this->data[$table]["receiver_district"]);
                            }
                            $this->data["selprintopt"][$k]["value"] = $addr . ' ' . $this->data[$table][$field];
                        } else {
                            $this->data["selprintopt"][$k]["value"] = $this->data[$table][$field];
                        }
                    }

                    $this->data["selkeys"] = array_keys($this->data["selprintopt"]);
                    $this->output("trade_ptexpress");
                } else {
                    $tradeid = strval($_GET['tradeid']);
                    $where = "tradeid='$tradeid'";
                    $this->data['trade'] = DB::getDB()->selectrow("trade", "*", $where);
                    $this->getDistrictopt($this->data['trade']['receiver_province'], $this->data['trade']['receiver_city'], $this->data['trade']['receiver_district']);

                    $this->data['order'] = DB::getDB()->select("order", "*", $where);
                    $addrid = isset($_GET['addrid']) ? trim($_GET['addrid']) : 0;
                    $this->data['addrlist'] = DB::getDB()->select("express_addr", "*", "isdel=0", "", "", "addrid");

                    $this->data['curaddr'] = array();
                    if ($addrid && isset($this->data['addrlist'][$addrid])) {
                        $this->data['curaddr'] = $this->data['addrlist'][$addrid];
                    } else {
                        foreach ($this->data['addrlist'] as $v) {
                            if ($v['getdefault']) {
                                $this->data['curaddr'] = $v;
                                break;
                            }
                        }
                    }
                    $this->data['tpllist'] = DB::getDB()->select("express_tpl", "*", "isdel=0", "isdefault");
                    $this->output("trade_preptexpress");
                }
                break;
            case 'savereceiver'://保存收件人
                $tradeid = strval($_POST["tradeid"]);
                $receiver_name = trim($_POST['name']);
                $receiver_province = trim($_POST['province']);
                $receiver_city = trim($_POST['city']);
                $receiver_district = trim($_POST['district']);
                $receiver_address = trim($_POST['address']);
                $receiver_zip = trim($_POST['zipcode']);
                $receiver_link = trim($_POST['link']);
                $data = array(
                    "receiver_name" => $receiver_name,
                    "receiver_province" => $receiver_province,
                    "receiver_city" => $receiver_city,
                    "receiver_district" => $receiver_district,
                    "receiver_address" => $receiver_address,
                    "receiver_zip" => $receiver_zip,
                    "receiver_link" => $receiver_link
                );
                $ret = DB::getDB()->update("trade", $data, "tradeid='$tradeid'");
                exit("success");
                break;
            case 'editfee'://改价
                $tradeid = trim($_GET['tradeid']);
                $this->data['trade'] = DB::getDB()->selectrow("trade", "tradeid,totalfee,itemfee,postfee,addtime,man,coupon,editfeetime,editfeememo", "tradeid='$tradeid'");
                $this->output("trade_editfee");
                break;
            case 'editfeeok'://确定改价
                $tradeid = trim($_POST['tradeid']);
                $editfeememo = trim($_POST["editfeememo"]);
                $totalfee = $_POST["totalfee"];
                $totalfee = getPrice($totalfee, 2, 'int');
                $data = array(
                    "editfeememo" => $editfeememo,
                    "totalfee" => $totalfee,
                    "editfeetime" => time()
                );
                $this->adminlog("al_trade", array("do" => "editfee", "tradeid" => $tradeid));
                DB::getDB()->update("trade", $data, "tradeid='" . $tradeid . "' AND status='WAIT_PAY'"); //只有未付款的订单才能改价
                exit("success");
                break;
            case 'pay'://支付
                $tradeid = trim($_GET['tradeid']);
                $this->data['trade'] = DB::getDB()->selectrow("trade", "tradeid,totalfee,itemfee,postfee,addtime,man,coupon,editfeetime,editfeememo", "tradeid='$tradeid'");
                $this->output("trade_pay");
                break;
            case 'payok'://确认支付
                $tradeid = trim($_POST['tradeid']);
                $time = time();
                $this->adminlog("al_trade", array("do" => "pay", "tradeid" => $tradeid));
                DB::getDB()->update("trade", "status='WAIT_SEND',paytime='$time'", "tradeid='" . $tradeid . "' AND status='WAIT_PAY'");
                exit("success");
                break;
            case 'send'://发货
                $tradeid = trim($_GET['tradeid']);
                $this->data['trade'] = DB::getDB()->selectrow("trade", "*", "tradeid='$tradeid'");
                $companies = DB::getDB()->selectkv("express_company", "companyid", "company", "isdel=0", "order");
                $this->data['companyopt'] = array2select($companies, "key", "val", null);

                $this->data['way'] = DB::getDB()->selectval("express_way", "name", "wayid='" . $this->data['trade']['expresswayid'] . "'");
                $this->output("trade_send");
                break;
            case 'sendok'://确认发货
                $tradeid = trim($_POST['tradeid']);
                $sendno = trim($_POST["sendno"]);
                $companyid = trim($_POST["companyid"]);
                $time = time();
                $trade = DB::getDB()->selectrow("trade", "*", "tradeid='$tradeid'");
                if ($trade) {
                    if ($trade['payment'] == "alipay") {//如果使用支付宝担保交易，通知支付宝已经发货
                        $alipay = PayTrade::getInstance('alipay');
                        $company = DB::getDB()->selectval("express_company", "company", "companyid='$companyid'");

                        $aliret = $alipay->send($trade['outtradeid'], $company, $sendno);
                        if ($aliret && $aliret['trade_status'] == "WAIT_BUYER_CONFIRM_GOODS") {//支付宝发货成功后，同步本地的发货
                            $tradeid = $aliret['out_trade_no']; //更改此处tradeid为支付宝返回的tradeid
                            $data = array("tradeid" => $tradeid, "sendno" => $sendno, "companyid" => $companyid, "sendtime" => $time);
                            $this->adminlog("al_trade", array("do" => "send", "tradeid" => $tradeid));
                            DB::getDB()->insert("trade_send", $data);
                            DB::getDB()->update("trade", "status='WAIT_RECE',sendtime='$time'", "tradeid='$tradeid'");
                            $ret = "success";
                        } else {
                            $ret = __("alipay_send_error");
                        }
                    } else {
                        $data = array("tradeid" => $tradeid, "sendno" => $sendno, "companyid" => $companyid, "sendtime" => $time);
                        $this->adminlog("al_trade", array("do" => "send", "tradeid" => $tradeid));

                        DB::getDB()->insert("trade_send", $data);
                        DB::getDB()->update("trade", "status='WAIT_RECE',sendtime='$time'", "tradeid='$tradeid'");
                        $ret = "success";
                    }
                } else {
                    $ret = __("send_error");
                }
                if ($ret == "success") {
                    $mq = new MQ("tradesend");
                    $company = DB::getDB()->selectval("express_company", "company", "companyid='$companyid'");
                    $mq->send($trade["uid"], array(
                        "mobile" => $trade['receiver_link'],
                        "replacement" => array($tradeid, $company, $sendno),
                    ));
                }
                exit($ret);
                break;
            case 'finish'://订单完成
                $tradeid = trim($_POST["tradeid"]);
                $where = "tradeid='$tradeid'";
                $trade = DB::getDB()->selectrow("trade", "*", $where);
                $orders = DB::getDB()->select("order", "*", $where);
                $adddata = array();
                $time = time();
                $itemids = $productids = array();
                foreach ($orders as $order) {
                    $adddata[] = array(
                        "tradeid" => $trade['tradeid'],
                        "uid" => $trade['uid'],
                        "uname" => $trade['uname'],
                        "itemid" => $order['itemid'],
                        "productid" => $order['productid'],
                        "pbn" => $order["pbn"],
                        "ibn" => $order["ibn"],
                        "itemname" => $order["itemname"],
                        "num" => $order["num"],
                        "price" => $order['price'],
                        "itemimg" => $order["itemimg"],
                        "saletime" => $trade['addtime'],
                        "finishtime" => $time
                    );
                    $itemids[$order['itemid']] = $order['num'];
                    $order['productid'] && ( $productids[$order['productid']] = $order['num'] );
                }
                if ($adddata) {
                    //销售记录
                    DB::getDB()->insertMulti("sales", $adddata);
                    $this->adminlog("al_trade", array("do" => "finish", "tradeid" => $tradeid));

                    //更新订单状态
                    DB::getDB()->update("trade", "status='FINISH',isfinish=1,endtime='$time'", $where);
                    foreach ($itemids as $itemid => $num) {
                        if (!$itemid || !$num)
                            continue;
                        DB::getDB()->updatecremulti("item", array("volume" => "+ $num", "inventory" => "- $num", "modified" => $time), "itemid='$itemid'");
                    }
                    foreach ($productids as $productid => $num) {
                        if (!$productid || !$num)
                            continue;
                        DB::getDB()->updatecremulti("product", array("volume" => "+ $num", "inventory" => "- $num"), "productid='$productid'");
                    }
                }
                exit("success");
                break;
            case 'cancel'://订单作废
                $tradeid = trim($_POST["tradeid"]);
                $trade = DB::getDB()->selectrow("trade", "coupon,receiver_link,uid", "tradeid='$tradeid'");
                if ($trade) {
                    $this->adminlog("al_trade", array("do" => "cancel", "tradeid" => $tradeid));
                    //更新订单状态
                    DB::getDB()->update("trade", "status='CANCELED',iscancel=1,isfinish=1", "tradeid='$tradeid'");
                    //订单作废后，如果使用了优惠券，优惠券恢复可用
                    if ($trade['coupon']) {
                        DB::getDB()->update("user_coupon", "tradeid='',isused=0", "isused=1 AND tradeid='$tradeid'");
                    }
                    //发消息
                    $mq = new MQ("tradeclose");
                    $mq->send($trade['uid'], array(
                        "mobile" => $trade['receiver_link'],
                        "replacement" => array($tradeid)));
                }
                exit("success");
                break;
            case 'editfield'://订单移到回收站
                $field = trim($_POST["field"]);
                if ($field == "remove") {
                    $tradestr = $_POST["idstr"];
                    $ret = false;
                    if ($tradestr) {
                        $tradeids = explode(',', $tradestr);
                        $where = "tradeid in " . cimplode($tradeids);
                        //更新trade和order
                        $ret = DB::getDB()->update("trade", "isdel=1", $where);

                        DB::getDB()->update("order", "isdel=1", $where);

                        //入库
                        $recycledata = array();
                        $table = array("table" => "trade", "type" => "trade", "tablefield" => "tradeid");

                        foreach ($tradeids as $tradeid) {
                            $this->adminlog("al_trade", array("do" => "remove", "tradeid" => $tradeid));
                            $recycledata[] = $table + array("tableid" => $tradeid, "title" => $tradeid);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                    exit($ret ? "success" : "failure");
                }
                exit("failure");
                break;
            case 'kuaidi'://kuaidi100查询
                $kuaidi_status = getConfig("kuaidi_status");
                if (!$kuaidi_status) {
                    $this->data['content']['message'] = __("not_open_kuaidi_service");
                } else {
                    $tradeid = trim($_GET["tradeid"]);
                    $tradesend = DB::getDB()->selectrow("trade_send", "*", "tradeid='$tradeid'");
                    $this->data['content'] = array();
                    if ($tradesend) {
                        $company = DB::getDB()->selectrow("express_company", "*", "companyid='" . $tradesend['companyid'] . "'");
                        $nu = $tradesend['sendno'];
                        $com = $company['kuaidi'];
                        $key = getConfig("kuaidi_key"); //"4a2a8765a7e09ec3"
                        if ($nu && $com && $key) {
                            $this->data["nu"] = $nu;
                            $this->data["com"] = $com;
                            $url = "http://api.kuaidi100.com/api?id=$key&com=$com&nu=$nu&order=asc";
                            $this->data['content'] = @json_decode(file_get_contents($url), true);
                        } else {
                            $this->data['content']['message'] = __("needinfo_empty_cannt_seach");
                        }
                    } else {
                        $this->data['content']['message'] = __("not_send_trade");
                    }
                }
                $this->output("trade_kuaidi");
                break;
        }
    }

}
