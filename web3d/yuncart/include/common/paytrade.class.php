<?php

class PayTrade
{

    private static $instance = array();

    public static function getInstance($code)
    {
        if (isset(self::$instance[$code]))
            return self::$instance[$code];

        if ($code == "tenpay" || $code == "tenpay2") {
            require THIRDPATH . '/payment/tenpay/tenpay.class.php';
            $config = self::getConfig($code);
            self::$instance[$code] = new Tenpay($config);
            return self::$instance[$code];
        } else if ($code == "alipay") {
            require THIRDPATH . '/payment/alipay/alipay.class.php';
            $config = self::getConfig($code);
            self::$instance[$code] = new Alipay($config);
            return self::$instance[$code];
        }
    }

    public static function getConfig($code)
    {
        switch ($code) {
            case 'tenpay':
            case 'tenpay2':
                $payment = DB::getDB()->selectrow("payment", "*", "code='$code'");
                $return = getConfig("weburl") . url("index", 'payment', 'preturn', 'code=' . $code);
                $notify = getConfig("weburl") . url("index", 'payment', 'pnotify', 'code=' . $code);

                if (!$payment || !$payment['paysecret'] || !$payment['ispublish'] || !$payment['account']) {
                    cerror(__("cannt_pay_by", getCommonCache($code, "payment")));
                }
                return array(
                    "partner" => $payment['account'],
                    "key" => $payment['paysecret'],
                    "return" => $return,
                    "notify" => $notify,
                    "code" => $code
                );
                break;
            case 'alipay':
                $payment = DB::getDB()->selectrow("payment", "*", "code='$code'");
                $return = getConfig("weburl") . url("index", 'payment', 'preturn', 'code=' . $code);
                $notify = getConfig("weburl") . url("index", 'payment', 'pnotify', 'code=' . $code);
                if (!$payment || !$payment['paykey'] || !$payment['paysecret'] || !$payment['ispublish'] || !$payment['account']) {
                    cerror(__("cannt_pay_by", getCommonCache("alipay", "payment")));
                }
                return array(
                    "partner" => $payment["paykey"],
                    "key" => $payment["paysecret"],
                    "seller_email" => $payment["account"],
                    "transport" => 'http',
                    "input_charset" => 'utf-8',
                    "sign_type" => 'MD5',
                    "return" => $return,
                    "notify" => $notify,
                    "code" => $code
                );
                break;
        }
    }

    public static function setTrade($tradeid, $para, $error = false)
    {
        $trade = DB::getDB()->selectrow("trade", "uid,uname,addtime,status,totalfee,receiver_link", "tradeid='$tradeid'");
        if (!$trade)
            return true; //交互直接返回true
        $setstatus = $para['status'];
        if ($setstatus == "WAIT_SEND" && $trade['status'] == "WAIT_PAY") {//修改为代发货,判断该订单必须为未支付
            if (isset($para['totalfee']) && $para['totalfee'] < $trade['totalfee'])
                return true; //如果有totalfee，但是价格小于订单价格



//本地订单状态必须为未支付，且价格小于等于
            $data = array(
                "status" => "WAIT_SEND",
                "paytime" => time(),
                "outtradeid" => $para['outtradeid']
            );
            DB::getDB()->update("trade", $data, "tradeid='$tradeid'"); //更新订单状态

            $mq = new MQ("tradepay");
            $mq->send($trade['uid'], array(
                "mobile" => $trade["receiver_link"],
                "replacement" => array($tradeid, getPrice($trade['totalfee']))
            ));
        }
        return true;
    }

}
