<?php
namespace wstmart\admin\model;
use think\Loader;
use think\Db;
use Env;
use wstmart\common\model\Payments as M;
use wstmart\common\model\LogPayParams as PM;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 微信支付业务处理
 */
class WeixinpaysApp extends Base{
	
	/**
	 * 初始化
	 */
	private $wxpayConfig;
	private $wxpay;
	public function initialize() {
		header ("Content-type: text/html; charset=utf-8");
	    require Env::get('root_path') . 'extend/wxpay/WxPayConf.php';
	    require Env::get('root_path') . 'extend/wxpay/WxJsApiPay.php';
		$this->wxpayConfig = array();
		$m = new M();
		$this->wxpay = $m->getPayment("app_weixinpays");
		$this->wxpayConfig['appid'] = $this->wxpay['appId']; // 微信公众号身份的唯一标识
		$this->wxpayConfig['appsecret'] = $this->wxpay['appsecret']; // JSAPI接口中获取openid
		$this->wxpayConfig['mchid'] = $this->wxpay['mchId']; // 受理商ID
		$this->wxpayConfig['key'] = $this->wxpay['apiKey']; // 商户支付密钥Key

		$this->wxpayConfig['apiclient_cert'] = WSTRootPath().'/extend/wxpay/cert2/apiclient_cert.pem'; // 商户支付证书
		$this->wxpayConfig['apiclient_key'] = WSTRootPath().'/extend/wxpay/cert2/apiclient_key.pem'; // 商户支付证书

		$this->wxpayConfig['curl_timeout'] = 30;
		$this->wxpayConfig['notifyurl'] = url("admin/orderrefunds/wxapprefundnodify","",true,true);
		$this->wxpayConfig['returnurl'] = "";
		// 初始化WxPayConf
		new \WxPayConf($this->wxpayConfig);
	}

	/**
	 * 退款
	 */
	public function orderRefund($refund,$order){

        $content = input('post.content');
        $refundId = (int)input('post.id');

        $wxrefund = new \Refund();
        $refund_no = $order['orderNo'].$order['userId'].$refund['serviceId'];
        $wxrefund->setParameter("transaction_id",$order['tradeNo']);//微信订单号
        $wxrefund->setParameter("out_refund_no",$refund_no);//商户退款单号
        $wxrefund->setParameter("total_fee",$order['totalPayFee']);//订单金额
        $wxrefund->setParameter("refund_fee",$refund["backMoney"]*100);//退款金额
        $wxrefund->setParameter("refund_fee_type","CNY");//货币种类
        $wxrefund->setParameter("refund_desc","订单【".$order['orderNo']."】退款");//退款原因
        $wxrefund->setParameter("notify_url",$this->wxpayConfig['notifyurl']);//退款原因

        $payParams = [];
        $payParams["userId"] = (int)$order['userId'];
        $payParams["refundId"] = $refundId;
        $payParams["isBatch"] = (int)$order['isBatch'];
        $payParams["content"] = $content;
        $pdata = array();
        $pdata["userId"] = $order['userId'];
        $pdata["transId"] = $refund_no;
        $pdata["paramsVa"] = json_encode($payParams);
        $pdata["payFrom"] = 'app_weixinpays';
        $m = new PM();
        $m->addPayLog($pdata);
        $rs = $wxrefund->getResult();
		if($rs["result_code"]=="SUCCESS"){
			return WSTReturn("退款成功",1); 
		}else{
			return WSTReturn($rs['err_code_des'],-1); 
		}
    }

    /**
     * 异步通知
     */
   	public function notify(){
   		// 使用通用通知接口
		$notify = new \Notify();
		// 存储微信的回调
		$xml = file_get_contents("php://input");
		$notify->saveData ( $xml );
		if ($notify->data ["return_code"] == "SUCCESS"){
			$order = $notify->getData ();
			$req_info = $order["req_info"];

			$reqinfo = $notify->decryptReqinfo($req_info);//解密退款加密信息
			$transId = $reqinfo["out_refund_no"];
			$m = new PM();
          	$payParams = $m->getPayLog(["transId"=>$transId]);
          	$content = $payParams['content'];
      		$refundId = $payParams['refundId'];

	   		$obj = array();
	        $obj['refundTradeNo'] = $reqinfo["refund_id"];//微信退款单号
	        $obj['content'] = $content;
	        $obj['refundId'] = $refundId;
	        $rs = model('admin/OrderRefunds')->complateOrderRefund($obj);
	        if($rs['status']==1){
	        	echo "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
	        }else{
	        	echo "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>";
	        }
	    }
   	}
	

}
