<?php
//---------------------------------------------------------
//财付通即时到帐支付请求示例，商户按照此文档进行开发即可
//---------------------------------------------------------

require_once ("classes/RequestHandler.class.php");
require_once ("tenpay_config.php");

/* 支付银行 */
$bank_type_value=$_REQUEST["bank_type"];

/* 获取提交的订单号 */
$curDateTime = date("YmdHis");
$randNum = rand(1000, 9999);
$out_trade_no = mc_user_id() . $curDateTime . $randNum;
/* 获取提交的商品名称 */
$product_name = mc_option('site_name').'订单';

$now = strtotime("now");
		$cart = M('action')->where("user_id='".mc_user_id()."' AND action_key='cart'")->getField('id',true);
		if($cart) {
			$action['date'] = $now;
			M('action')->where("user_id='".mc_user_id()."' AND action_key='cart'")->save($action);
			M('action')->where("user_id='".mc_user_id()."' AND action_key='address_pending'")->delete();
			M('action')->where("user_id='".mc_user_id()."' AND action_key='trade_pending'")->delete();
			M('action')->where("user_id='".mc_user_id()."' AND action_key='coins_pending'")->delete();
			//mc_add_action(mc_user_id(),'address_pending','<h5>'.$_POST['buyer_name'].'</h5><p>'.$_POST['buyer_province'].'</p><p>'.$_POST['buyer_city'].'</p><p>'.$_POST['buyer_address'].'</p><p>'.$_POST['buyer_phone'].'</p>');
			//mc_add_action(mc_user_id(),'out_trade_no',$out_trade_no);
			$action['page_id'] = mc_user_id();
			$action['user_id'] = mc_user_id();
			$action['action_key'] = 'address_pending';
			$action['action_value'] = '<h4>'.I('param.buyer_name').'</h4><p>'.I('param.buyer_province').'，'.I('param.buyer_city').'，'.I('param.buyer_address').'</p><p>'.I('param.buyer_phone').'</p>';
			M('action')->data($action)->add();
			$action['action_key'] = 'trade_pending';
			$action['action_value'] = $out_trade_no;
			M('action')->data($action)->add();
			if(I('param.coins')>0 && I('param.coins')<=mc_coins(mc_user_id())) : //积分需大于0，且小于等于现有积分
				$coins_topthis = mc_total()*50;
				if(I('param.coins')<=$coins_topthis) :
					$action['action_key'] = 'coins_pending';
					$action['action_value'] = I('param.coins');
				endif;
			M('action')->data($action)->add();
			endif;
			$id = mc_user_id();
			mc_delete_meta($id,'buyer_name','user');
			if(I('param.buyer_name')) {
				      mc_add_meta($id,'buyer_name',I('param.buyer_name'),'user');
			        };
			        mc_delete_meta($id,'buyer_province','user');
					if(I('param.buyer_province')) {
				        mc_add_meta($id,'buyer_province',I('param.buyer_province'),'user');
			        };
			        mc_delete_meta($id,'buyer_city','user');
					if(I('param.buyer_city')) {
				        mc_add_meta($id,'buyer_city',I('param.buyer_city'),'user');
			        };
			        mc_delete_meta($id,'buyer_address','user');
					if(I('param.buyer_address')) {
				        mc_add_meta($id,'buyer_address',I('param.buyer_address'),'user');
			        };
			        mc_delete_meta($id,'buyer_phone','user');
					if(I('param.buyer_phone')) {
				        mc_add_meta($id,'buyer_phone',I('param.buyer_phone'),'user');
			        };
		} else {
			$this->error('购物车里没有任何商品！');
		};
/* 获取提交的商品价格 */
if(mc_total()<mc_option('m_youfei')) :
	$mc_total = mc_total()+mc_option('youfei');
else :
	$mc_total = mc_total();
endif;
if(I('param.coins')>0 && I('param.coins')<=mc_coins(mc_user_id())) : //积分需大于0，且小于等于现有积分
			$coins_topthis = mc_total()*50;
			if(I('param.coins')>$coins_topthis) :
				$mc_total2 = $mc_total;
			else :
				$mc_total2 = $mc_total-I('param.coins')/100;
			endif;
		else :
			$mc_total2 = $mc_total;
		endif;
$order_price = $mc_total2;
/* 获取提交的备注信息 */
$remarkexplain = $_REQUEST["remarkexplain"];
/* 支付方式 */
$trade_mode=1;

$strDate = date("Ymd");
$strTime = date("His");

/* 商品价格（包含运费），以分为单位 */
$total_fee = $order_price*100;

/* 商品名称 */
$desc = $product_name;

/* 创建支付请求对象 */
$reqHandler = new RequestHandler();
$reqHandler->init();
$reqHandler->setKey($key);
$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");

//----------------------------------------
//设置支付参数 
//----------------------------------------
$reqHandler->setParameter("partner", $partner);
$reqHandler->setParameter("out_trade_no", $out_trade_no);
$reqHandler->setParameter("total_fee", $total_fee);  //总金额
$reqHandler->setParameter("return_url", $return_url);
$reqHandler->setParameter("notify_url", $notify_url);
$reqHandler->setParameter("body", $desc);
//$reqHandler->setParameter("bank_type", "DEFAULT");  	  //银行类型，默认为财付通
$reqHandler->setParameter("bank_type", $bank_type_value);  	  //银行类型，默认为财付通
//用户ip
$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//客户端IP
$reqHandler->setParameter("fee_type", "1");               //币种
$reqHandler->setParameter("subject",$desc);          //商品名称，（中介交易时必填）

//系统可选参数
$reqHandler->setParameter("sign_type", "MD5");  	 	  //签名方式，默认为MD5，可选RSA
$reqHandler->setParameter("service_version", "1.0"); 	  //接口版本号
$reqHandler->setParameter("input_charset", "utf-8");   	  //字符集
$reqHandler->setParameter("sign_key_index", "1");    	  //密钥序号

//业务可选参数
$reqHandler->setParameter("attach", "");             	  //附件数据，原样返回就可以了
$reqHandler->setParameter("product_fee", "");        	  //商品费用
$reqHandler->setParameter("transport_fee", "0");      	  //物流费用
$reqHandler->setParameter("time_start", date("YmdHis"));  //订单生成时间
$reqHandler->setParameter("time_expire", "");             //订单失效时间
$reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
$reqHandler->setParameter("goods_tag", "");               //商品标记
$reqHandler->setParameter("trade_mode",$trade_mode);              //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
$reqHandler->setParameter("transport_desc","");              //物流说明
$reqHandler->setParameter("trans_type","1");              //交易类型
$reqHandler->setParameter("agentid","");                  //平台ID
$reqHandler->setParameter("agent_type","");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
$reqHandler->setParameter("seller_id","");                //卖家的商户号



//请求的URL
$reqUrl = $reqHandler->getRequestURL();

//获取debug信息,建议把请求和debug信息写入日志，方便定位问题
/**/
$debugInfo = $reqHandler->getDebugInfo();
//echo "<br/>" . $reqUrl . "<br/>";
//echo "<br/>" . $debugInfo . "<br/>";


?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="refresh" content="0.1;url=<?php echo $reqUrl ?>">
	<title>财付通接口跳转页面</title>
</head>
<body>
<form action="<?php echo $reqHandler->getGateUrl() ?>" method="post" target="_blank">
<?php
$params = $reqHandler->getAllParameters();
foreach($params as $k => $v) {
	echo "<input type=\"hidden\" name=\"{$k}\" value=\"{$v}\" />\n";
}
?>
<input type="submit" value="财付通支付">
</form>
</body>
</html>

