<?php

defined('IN_CART') or die;

/**
 *  
 * 支付
 *
 *
 * */
class Payment extends Base
{

    /**
     *  
     * 构造函数
     *
     *
     * */
    public function __construct($model, $action)
    {
        parent::__construct($model, $action);
    }

    /**
     *  
     * 支付
     *
     *
     * */
    public function index()
    {
        //支付需要登录
        if (empty($_SESSION["uid"])) {
            redirect(url("index", 'user', "login"));
        }
        $uid = $_SESSION["uid"];
        $tradeid = trim($_GET["tradeid"]);
        $code = trim($_GET["code"]);
        $trade = DB::getDB()->selectrow("trade", "tradeid,totalfee,status,memo,receiver_name,receiver_province,receiver_city,receiver_district,receiver_address,receiver_zip,receiver_link", "tradeid='$tradeid' AND uid='$uid'");

        if (!$trade || ($trade['status'] != 'WAIT_PAY')) {
            cerror(__("trade_cannt_pay"));
        }
        if ($code == "alipay") {
            $alipay = PayTrade::getInstance($code);
            $alipay->request($trade);
            exit();
        } else if ($code == "tenpay" || $code == "tenpay2") {
            $tenpay = PayTrade::getInstance($code);
            $tenpay->request($trade);
            exit();
        }
    }

    /**
     *  
     * 处理支付return_url
     *
     *
     * */
    public function preturn()
    {
        $code = strtolower(trim($_GET["code"]));
        unset($_GET["model"], $_GET["action"], $_GET["code"]);
        $ret = array();
        if ($code == "alipay") {
            $alipay = PayTrade::getInstance($code);
            $ret = $alipay->preturn();
        } else if ($code == "tenpay" || $code == "tenpay2") {
            $tenpay = PayTrade::getInstance($code);
            $ret = $tenpay->preturn();
        }
        if (!$ret)
            cerror(__("pay_error"));
        if ($ret["ret"]) {
            cerror(__("trade_pay_success", $ret['tradeid']));
        } else {
            cerror(__("trade_pay_failure", $ret['tradeid']));
        }
    }

    /**
     *  
     * 处理支付notify_url
     *
     *
     * */
    public function pnotify()
    {
        $code = strtolower(trim($_REQUEST["code"]));
        unset($_POST["model"], $_POST["action"], $_POST["code"]);
        if ($code == "alipay") {
            $alipay = PayTrade::getInstance($code);
            $alipay->pnotify();
            exit();
        } else if ($code == "tenpay" || $code == "tenpay2") {
            $tenpay = PayTrade::getInstance($code);
            $tenpay->pnotify();
            exit();
        }
    }

//
//	
//
//	/**
//	 *  
//	 * 相应
//	 *
//	 *
//	 **/
////	public function response() {
////		$code = strtolower(trim($_GET["code"]));
////		
////		//@file_put_contents("1.txt",var_export($_GET,true),FILE_APPEND);
////		unset($_GET['model'],$_GET['action'],$_GET['code']);
////
////		if($code == "alipay") {
////			$this->alipayres();
////		} else if($code == "tenpay") {
////			$this->tenpayres();header("Content-type:text/html;charset=utf-8");
////		require(THIRDPATH . "/payment/alipay/alipay_notify.class.php");
////		$aliapy_config		= $this->getAlipayConfig();
////		$alipayNotify		= new AlipayNotify($aliapy_config);
////		
////		$verify_result		= $alipayNotify->verifyReturn();
////		
////		if($verify_result) {
////			$status = trim($_GET['trade_status']);
////			if( $status == 'WAIT_SELLER_SEND_GOODS') {//支付成功，等待发货
////				$tradeid	 = trim($_GET["out_trade_no"]);
////				$outtradeid  = trim($_GET["trade_no"]);
////				$totalfee	 = trim($_GET["total_fee"]);
////				$this->updorder($tradeid,array("paytime"=>time(),"status"=>"WAIT_SEND","outtradeid"=>$outtradeid));
////				
////				$trade = DB::getDB()->selectrow("trade","uid,receiver_link","tradeid='$tradeid'");
////				$mq = new MQ("tradepay");
////				$mq->send($trade['uid'],array(
////					"mobile"		=> $trade["receiver_link"],
////					"replacement"	=> array($tradeid,getPrice($totalfee,0))
////				));
////				cerror(__("trade_pay_success",$tradeid));
////			} else {
////				echo "trade_status=".$_GET['trade_status'];
////			}
////		} else {
////			cerror(__("verify_error"));
////		}
////		} else {
////			cerror(__("access_error"));		
////		}
}
