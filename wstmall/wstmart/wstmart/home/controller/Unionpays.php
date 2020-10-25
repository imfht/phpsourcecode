<?php
namespace wstmart\home\controller;
use think\Loader;
use Env;
use wstmart\common\model\Payments as M;
use wstmart\common\model\Orders as OM;
use wstmart\common\model\LogMoneys as LM;
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
 * 银联支付控制器
 */
class Unionpays extends Base{
	
	/**
	 * 初始化
	 */
	private $unionConfig;
	public function initialize() {
		header ("Content-type: text/html; charset=utf-8");
		require Env::get('root_path') . 'extend/unionpay/sdk/acp_service.php';
		$m = new M();
		$this->unionConfig = $m->getPayment("unionpays");
	
		$config = array();
		$config["signCertPwd"] = $this->unionConfig["unionSignCertPwd"];//"000000"
		$config["signMethod"] = "01";
		$config["frontUrl"] = url("home/unionpays/response","",true,true);
		$config["backUrl"] = url("home/unionpays/notify","",true,true);
		new \SDKConfig($config);
	}
	
	
	public function getUnionpaysUrl(){
		$m = new OM();
		$payObj = input("payObj/s");
		$data = array();
		if($payObj=="recharge"){
			$needPay = input("needPay/d");
			$data["status"] = $needPay>0?1:-1;
		}else{
			$userId = (int)session('WST_USER.userId');
			$data = $m->checkOrderPay();
		}
		return $data;
	}
	
	/**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $config_value    支付方式信息
     */
    public function toUnionpays(){
    	
    	$payObj = input("payObj/s");
    	$m = new OM();
    	$obj = array();
    	$data = array();
    	$orderAmount = 0;
    	$orderId = "";
    	$extra_param = "";
    	if($payObj=="recharge"){//充值
    		
    		$itmeId = (int)input("itmeId/d");
    		$orderAmount = 0;
    		if($itmeId>0){
    			$item = $cm->getItemMoney($itmeId);
    			$orderAmount = isSet($item["chargeMoney"])?$item["chargeMoney"]:0;
    		}else{
    			$orderAmount = (int)input("needPay/d");
    		}
    		$shopId = (int)session('WST_USER.shopId');
    		$targetType = ($shopId>0)?1:0;
			$targetId = (int)session('WST_USER.userId');
			if($targetType==1){//商家
				$targetId = $shopId;
			}
    		
    		$data["status"] = $orderAmount>0?1:-1;
    		$orderId = WSTOrderNo();
    		$extra_param = $payObj."|".$targetId."|".$targetType."|".$itmeId;
    		
    	}else{
    		$obj["orderNo"] = input("orderNo/s");
    		$obj["isBatch"] = (int)input("isBatch/d");
    		$data = $m->checkOrderPay($obj);
    		if($data["status"]==1){
    			$userId = (int)session('WST_USER.userId');
    			$obj["userId"] = $userId;
    			$order = $m->getPayOrders($obj);
    			$orderAmount = $order["needPay"];
    			$payRand = $order["payRand"];
    			$orderId = $obj["orderNo"]."a".$payRand;
    			$extra_param = $payObj."|".$userId."|".$obj["isBatch"];
    		}
    	}
    	
    	if($data["status"]==1){
	    	$params = array(
	    		//以下信息非特殊情况不需要改动
	    		'version' => \SDKConfig::$version,                 //版本号
	    		'encoding' => 'utf-8',				  //编码方式
	    		'txnType' => '01',				      //交易类型
	    		'txnSubType' => '01',				  //交易子类
	    		'bizType' => '000201',				  //业务类型
	    		'frontUrl' =>  \SDKConfig::$frontUrl,  //前台通知地址
	    		'backUrl' => \SDKConfig::$backUrl,	  //后台通知地址
	    		'signMethod' => \SDKConfig::$signMethod,//签名方法
	    		'channelType' => '07',	              //渠道类型，07-PC，08-手机
	    		'accessType' => '0',		          //接入类型
	    		'currencyCode' => '156',	          //交易币种，境内商户固定156
	    		//TODO 以下信息需要填写
	    		'merId' => $this->unionConfig["unionMerId"], //"777290058110048",//商户代码
	    		'orderId' => $orderId,	//商户订单号，8-32位数字字母，不能含“-”或“_”
	    		'txnTime' => date('YmdHis'),	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间
	    		'txnAmt' => $orderAmount*100,	//交易金额，单位分，此处默认取demo演示页面传递的参数
	    		// 订单超时时间。
	    		//'payTimeout' => date('YmdHis', strtotime('+15 minutes')),
	    	
	    		'reqReserved' => $extra_param,
	    	);
	    	$acpService = new \AcpService();
	    	$acpService::sign ( $params );
	    	$uri = \SDKConfig::$frontTransUrl;
	    	$html_form = $acpService::createAutoFormHtml( $params, $uri );
	    	echo $html_form;
    	}else{
    		
    	}
    }
    
    /**
     * 异步回调接口
     */
    public function notify(){                
      
        //计算得出通知验证结果        
        $acpService = new \AcpService(); // 使用银联原生自带的累 和方法 这里只是引用了一下 而已
        $verify_result = $acpService->validate($_POST);
        
     	if($verify_result){//验证成功
         	$out_trade_no = $_POST['orderId']; //商户订单号                    
            $queryId = $_POST['queryId']; //银联支付流水号
            // 解释: 交易成功且结束，即不可再做任何操作。
           	if($_POST['respMsg'] == 'Success!'){                    
	           	$m = new OM();
				$extras = explode("|",$_POST['reqReserved']);
				$rs = array();
				if($extras[0]=="recharge"){//充值
					$targetId = (int)$extras [1];
					$targetType = (int)$extras [2];
					$itemId = (int)$extras [3];
					$obj = array ();
					$obj["trade_no"] = $_POST['trade_no'];
					$obj["out_trade_no"] = $_POST["out_trade_no"];;
					$obj["targetId"] = $targetId;
					$obj["targetType"] = $targetType;
					$obj["itemId"] = $itemId;
					$obj["total_fee"] = $_POST['total_fee'];
					$obj["payFrom"] = 'unionpays';
					// 支付成功业务逻辑
					$m = new LM();
					$rs = $m->complateRecharge ( $obj );
				}else{
					//商户订单号
					$obj = array();
					$tradeNo = explode("a",$out_trade_no);
					$obj["trade_no"] = $_POST['trade_no'];
					$obj["out_trade_no"] = $tradeNo[0];
					$obj["total_fee"] = $_POST['total_fee'];
						
					$obj["userId"] = $extras[1];
					$obj["isBatch"] = $extras[2];
					$obj["payFrom"] = 'unionpays';
					//支付成功业务逻辑
					$rs = $m->complatePay($obj);
				}
				if($rs["status"]==1){
					echo 'success';
				}else{
					echo 'fail';
				}
      		}
  		}else{                
      		echo "fail"; //验证失败                                
  		}
    }
    
    /**
     * 同步回调接口
     */
    public function response(){
        //计算得出通知验证结果        
        $acpService = new \AcpService(); // 使用银联原生自带的累 和方法 这里只是引用了一下 而已
        $verify_result = $acpService->validate($_POST);
        
   		if($verify_result){ //验证成功
       		$order_sn = $out_trade_no = $_POST['orderId']; //商户订单号
        	$queryId = $_POST['queryId']; //银联支付流水号                   
          	$respMsg = $_POST['respMsg']; //交易状态
                    
      		if($_POST['respMsg'] == 'success'){
      			$m = new OM();
      			$extras = explode("|",$_POST['extra_param']);
   				if($extras[0]=="recharge"){//充值
   					if($extras[2]==1){
   						$this->redirect(url("home/logmoneys/shopmoneys"));
   					}else{
   						$this->redirect(url("home/logmoneys/usermoneys"));
   					}
   				}else{
   					$obj = array();
   					$tradeNo = explode("a",$out_trade_no);
   					$obj["orderNo"] = $tradeNo[0];
   					$obj["userId"] = $extras[1];
   					$obj["isBatch"] = $extras[2];
   					$rs = $m->getOrderType($obj);
   					if((int)$rs["orderType"]==1){
   						$this->redirect(url("home/orders/waitAppraise"));
   					}else{
   						$this->redirect(url("home/orders/waitReceive"));
   					}
   				}
       		}else {                        
           		$this->error('支付失败');
   			}
     	}else {                     
     		$this->error('支付失败');
 		}
    }

}
