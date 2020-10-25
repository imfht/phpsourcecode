<?php

//财付通
class Tenpay
{

    private $partner; //财付通商户号
    private $key; //财付通密钥
    private $return_url; //显示支付结果页面,*替换成payReturnUrl.php所在路径
    private $notify_url; //支付完成后的回调处理页面,*替换成payNotifyUrl.php所在路径
    private $code = '';

    //构造函数
    public function __construct($config)
    {
        $this->partner = $config['partner'];
        $this->key = $config['key'];
        $this->return_url = $config['return'];
        $this->notify_url = $config['notify'];
        $this->code = $config['code'];
    }

    //请求
    public function request($trade)
    {
        $trade_mode = ($this->code == "tenpay") ? "1" : "2";

        $out_trade_no = $trade['tradeid'];
        $product_name = $trade['tradeid'];
        $total_fee = getPrice($trade["totalfee"], -2, "float") * 100;
        $remarkexplain = $trade['memo'];


        $desc = "商品：" . $product_name . ",备注:" . $remarkexplain;

        require(THIRDPATH . "/payment/tenpay/classes/RequestHandler.class.php");

        $reqHandler = new RequestHandler();
        $reqHandler->init();
        $reqHandler->setKey($this->key);
        $reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");

        //----------------------------------------
        //设置支付参数 
        //----------------------------------------
        $reqHandler->setParameter("partner", $this->partner);
        $reqHandler->setParameter("out_trade_no", $out_trade_no);
        $reqHandler->setParameter("total_fee", $total_fee);  //总金额
        $reqHandler->setParameter("return_url", $this->return_url);
        $reqHandler->setParameter("notify_url", $this->notify_url);
        $reqHandler->setParameter("body", $desc);
        $reqHandler->setParameter("bank_type", "DEFAULT");     //银行类型，默认为财付通
        //用户ip
        $reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']); //客户端IP
        $reqHandler->setParameter("fee_type", "1");               //币种
        $reqHandler->setParameter("subject", $desc);     //商品名称，（中介交易时必填）
        //系统可选参数
        $reqHandler->setParameter("sign_type", "MD5");       //签名方式，默认为MD5，可选RSA
        $reqHandler->setParameter("service_version", "1.0");    //接口版本号
        $reqHandler->setParameter("input_charset", "utf-8");      //字符集
        $reqHandler->setParameter("sign_key_index", "1");       //密钥序号
        //业务可选参数
        $reqHandler->setParameter("attach", "");                //附件数据，原样返回就可以了
        $reqHandler->setParameter("product_fee", "");           //商品费用
        $reqHandler->setParameter("transport_fee", "0");         //物流费用
        $reqHandler->setParameter("time_start", date("YmdHis"));  //订单生成时间
        $reqHandler->setParameter("time_expire", "");             //订单失效时间
        $reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
        $reqHandler->setParameter("goods_tag", "");               //商品标记
        $reqHandler->setParameter("trade_mode", $trade_mode);      //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
        $reqHandler->setParameter("transport_desc", "");           //物流说明
        $reqHandler->setParameter("trans_type", "1");              //交易类型
        $reqHandler->setParameter("agentid", "");                  //平台ID
        $reqHandler->setParameter("agent_type", "");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
        $reqHandler->setParameter("seller_id", "");                //卖家的商户号
        $reqHandler->createSign();

        header("Content-type:text/html;charset=utf-8");
        $html = "<form action='" . $reqHandler->getGateUrl() . "' method='post' id='tenpaysubmit' name='tenpaysubmit'>";
        $params = $reqHandler->getAllParameters();
        foreach ($params as $k => $v) {
            $html .= "<input type='hidden' name='{$k}' value='{$v}' />\n";
        }
        $html .= "<input type='submit' value='Redirecting...'>"
                . "</form>"
                . "<script>document.forms['tenpaysubmit'].submit();</script>";
        echo $html;
    }

    //return url
    public function preturn()
    {
        require_once (THIRDPATH . "/payment/tenpay/classes/ResponseHandler.class.php");
        $resHandler = new ResponseHandler();
        $resHandler->setKey($this->key);
        if ($resHandler->isTenpaySign()) {
            //通知id
            $notify_id = $resHandler->getParameter("notify_id");
            //商户订单号
            $out_trade_no = $resHandler->getParameter("out_trade_no");
            //财付通订单号
            $transaction_id = $resHandler->getParameter("transaction_id");
            //金额,以分为单位
            $total_fee = $resHandler->getParameter("total_fee");
            //如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
            $discount = $resHandler->getParameter("discount");
            //支付结果
            $trade_state = $resHandler->getParameter("trade_state");
            //交易模式,1即时到账
            $trade_mode = $resHandler->getParameter("trade_mode");
            return array("tradeid" => $out_trade_no, "ret" => "0" == $trade_state ? true : false);
        }
        return false;
    }

    //notify_url
    public function pnotify()
    {
        require(THIRDPATH . "/payment/tenpay/classes/RequestHandler.class.php");
        require(THIRDPATH . "/payment/tenpay/classes/ResponseHandler.class.php");
        require (THIRDPATH . "/payment/tenpay/classes/client/ClientResponseHandler.class.php");
        require (THIRDPATH . "/payment/tenpay/classes/client/TenpayHttpClient.class.php");

        $resHandler = new ResponseHandler();
        $resHandler->setKey($this->key);
        $trade_mode = $resHandler->getParameter("trade_mode");

        if ($resHandler->isTenpaySign()) {
            $notify_id = $resHandler->getParameter("notify_id");

            $queryReq = new RequestHandler();
            $queryReq->init();
            $queryReq->setKey($this->key);
            $queryReq->setGateUrl("https://gw.tenpay.com/gateway/simpleverifynotifyid.xml");
            $queryReq->setParameter("partner", $this->partner);
            $queryReq->setParameter("notify_id", $notify_id);

            $httpClient = new TenpayHttpClient();
            $httpClient->setTimeOut(30);
            //设置请求内容
            $httpClient->setReqContent($queryReq->getRequestURL());

            if ($httpClient->call()) {
                $queryRes = new ClientResponseHandler();
                $queryRes->setContent($httpClient->getResContent());
                $queryRes->setKey($this->key);
                if ($trade_mode == "1") {
                    if ($queryRes->isTenpaySign() && $queryRes->getParameter("retcode") == "0" && $resHandler->getParameter("trade_state") == "0") {
                        $out_trade_no = $resHandler->getParameter("out_trade_no");
                        $transaction_id = $resHandler->getParameter("transaction_id");
                        $total_fee = $resHandler->getParameter("total_fee");
                        $discount = $resHandler->getParameter("discount");
                        //订单修改状态为未发货
                        PayTrade::setTrade($out_trade_no, array("status" => "WAIT_SEND",
                            "totalfee" => $total_fee,
                            "outtradeid" => $transaction_id
                        ));
                        exit("success");
                    } else {
                        exit("fail");
                    }
                } elseif ($trade_mode == "2") {
                    if ($queryRes->isTenpaySign() && $queryRes->getParameter("retcode") == "0") {
                        $out_trade_no = $resHandler->getParameter("out_trade_no"); //取结果参数做业务处理
                        $transaction_id = $resHandler->getParameter("transaction_id"); //财付通订单号
                        switch ($resHandler->getParameter("trade_state")) {
                            case "0": //付款成功
                                //订单修改状态为未发货
                                PayTrade::setTrade($out_trade_no, array("status" => "WAIT_SEND",
                                    "outtradeid" => $transaction_id
                                ));
                                break;
                            case "1": //交易创建

                                break;
                            case "2": //收获地址填写完毕

                                break;
                            case "4": //卖家发货成功
//									PayTrade::setTrade($out_trade_no,array("status"			=>"WAIT_RECE",
//																		   "outtradeid"	    =>$transaction_id
//									));
                                break;
                            case "5": //买家收货确认，交易成功

                                break;
                            case "6": //交易关闭，未完成超时关闭

                                break;
                            case "7": //修改交易价格成功

                                break;
                            case "8": //买家发起退款

                                break;
                            case "9": //退款成功

                                break;
                            case "10": //退款关闭			

                                break;
                            default:
                                break;
                        }
                        exit("success");
                    } else {
                        exit("fail");
                    }
                }
            }
        }
        exit("fail");
    }

}
