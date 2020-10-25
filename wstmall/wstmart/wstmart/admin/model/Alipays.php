<?php
namespace wstmart\admin\model;
use think\Loader;
use think\Db;
use Env;
use wstmart\common\model\Payments as M;
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
 * 阿里支付控制器
 */
class Alipays extends Base{

	/**
	 * 退款
	 */
	public function orderRefund($refund,$order){

        $content = input('post.content');
        $refundId = (int)input('post.id');
        $request_no = $order['orderNo'].$order['userId'].$refund['serviceId'];
        $backMoney = $refund["backMoney"];
        $tradeNo = $order['tradeNo'];
        $refund_reason = "订单【".$order['orderNo']."】退款";
        
        require Env::get('root_path') . 'extend/alipay/aop/AopClient.php';
	   	require Env::get('root_path') . 'extend/alipay/aop/request/AlipayTradeRefundRequest.php';
        $m = new M();
	   	$payment = $m->getPayment("alipays");
        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $payment["appId"];
        $aop->rsaPrivateKey = $payment["rsaPrivateKey"];
        $aop->alipayrsaPublicKey=$payment["alipayrsaPublicKey"];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \AlipayTradeRefundRequest ();

        $request->setBizContent("{" .
            "\"trade_no\":\"$tradeNo\"," .
            "\"refund_amount\":\"$backMoney\"," .
            "\"refund_reason\":\"$refund_reason\"," .
            "\"out_request_no\":\"$request_no\"" .
        "  }");

        $result = $aop->execute ( $request); 

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode) && $resultCode == 10000){
        	if($result->$responseNode->fund_change=="Y"){
        		$obj = array();
		        $obj['refundTradeNo'] = $request_no;//退款单号
		        $obj['content'] = $content;
		        $obj['refundId'] = $refundId;
		        $rs = model('admin/OrderRefunds')->complateOrderRefund($obj);
		        if($rs['status']==1){
		        	return WSTReturn("退款成功",1); 
		        }else{
		        	return WSTReturn("退款失败",1);
		        }
        	}
        } else {
        	$msg = $result->$responseNode->sub_msg;
            return WSTReturn($msg,-1); 
        }
    }

    /**
     * 入驻不通过，退款年费
     */
    public function enterRefund($obj){
        $request_no = $obj['orderNo'].$obj['userId'];
        $backMoney = $obj['money'];
        $tradeNo = $obj['tradeNo'];
        $refund_reason = "店铺入驻审核不通过，退回店铺缴纳年费";

        require Env::get('root_path') . 'extend/alipay/aop/AopClient.php';
        require Env::get('root_path') . 'extend/alipay/aop/request/AlipayTradeRefundRequest.php';
        $m = new M();
        $payment = $m->getPayment("alipays");
        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $payment["appId"];
        $aop->rsaPrivateKey = $payment["rsaPrivateKey"];
        $aop->alipayrsaPublicKey=$payment["alipayrsaPublicKey"];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \AlipayTradeRefundRequest ();

        $request->setBizContent("{" .
            "\"trade_no\":\"$tradeNo\"," .
            "\"refund_amount\":\"$backMoney\"," .
            "\"refund_reason\":\"$refund_reason\"," .
            "\"out_request_no\":\"$request_no\"" .
            "  }");

        $result = $aop->execute ( $request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode) && $resultCode == 10000){
            if($result->$responseNode->fund_change=="Y"){
                $reObj = array();
                $reObj['userId'] = $obj['userId'];
                $rs = model('admin/shops')->completeEnterRefund($reObj);
                if($rs['status']==1){
                    return WSTReturn("操作成功，年费退款成功",1);
                }else{
                    return WSTReturn("年费退款失败",1);
                }
            }
        } else {
            $msg = $result->$responseNode->sub_msg;
            return WSTReturn($msg,-1);
        }
    }

    /**
     * 供货商入驻不通过，退款年费
     */
    public function supplierEnterRefund($obj){
        $request_no = $obj['orderNo'].$obj['userId'];
        $backMoney = $obj['money'];
        $tradeNo = $obj['tradeNo'];
        $refund_reason = "供货商入驻审核不通过，退回供货商缴纳年费";

        require Env::get('root_path') . 'extend/alipay/aop/AopClient.php';
        require Env::get('root_path') . 'extend/alipay/aop/request/AlipayTradeRefundRequest.php';
        $m = new M();
        $payment = $m->getPayment("alipays");
        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $payment["appId"];
        $aop->rsaPrivateKey = $payment["rsaPrivateKey"];
        $aop->alipayrsaPublicKey=$payment["alipayrsaPublicKey"];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \AlipayTradeRefundRequest ();

        $request->setBizContent("{" .
            "\"trade_no\":\"$tradeNo\"," .
            "\"refund_amount\":\"$backMoney\"," .
            "\"refund_reason\":\"$refund_reason\"," .
            "\"out_request_no\":\"$request_no\"" .
            "  }");

        $result = $aop->execute ( $request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode) && $resultCode == 10000){
            if($result->$responseNode->fund_change=="Y"){
                $reObj = array();
                $reObj['userId'] = $obj['userId'];
                $rs = model('admin/suppliers')->completeEnterRefund($reObj);
                if($rs['status']==1){
                    return WSTReturn("操作成功，年费退款成功",1);
                }else{
                    return WSTReturn("年费退款失败",1);
                }
            }
        } else {
            $msg = $result->$responseNode->sub_msg;
            return WSTReturn($msg,-1);
        }
    }
}
