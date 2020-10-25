<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>支付宝即时到账交易接口接口</title>
</head>
<?php
/* *
 * 功能：即时到账交易接口接入页
 * 版本：3.3
 * 修改日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*************************
 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */

require_once("alipay.config.php");
require_once("lib/alipay_submit.class.php");



/**************************调用授权接口alipay.wap.trade.create.direct获取授权码token**************************/
	
//返回格式
$format = "xml";
//必填，不需要修改

//返回格式
$v = "2.0";
//必填，不需要修改

//请求号
$req_id = date('Ymdhis');
//必填，须保证每次请求都是唯一

//**req_data详细信息**

//服务器异步通知页面路径
$notify_url = mc_option('site_url').'/pay/alipay_wap_notify_url.php';
//需http://格式的完整路径，不允许加?id=123这类自定义参数

//页面跳转同步通知页面路径
$call_back_url = mc_option('site_url').'/pay/alipay_wap_return_url.php';
//需http://格式的完整路径，不允许加?id=123这类自定义参数

//操作中断返回地址
$merchant_url = mc_option('site_url');
//用户付款中途退出返回商户的地址。需http://格式的完整路径，不允许加?id=123这类自定义参数

//卖家支付宝帐户
$seller_email = mc_option('alipay_wap_seller');
//必填

//商户订单号
$curDateTime = date("YmdHis");
$randNum = rand(1000, 9999);
$out_trade_no = mc_user_id() . $curDateTime . $randNum;
//商户网站订单系统中唯一订单号，必填

//订单名称
$subject = mc_option('site_name').'订单';
//必填

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

//付款金额
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
		$total_fee = $mc_total2;
//必填

//请求业务参数详细
$req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . $seller_email . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee><merchant_url>' . $merchant_url . '</merchant_url></direct_trade_create_req>';
//必填

/************************************************************/

//构造要请求的参数数组，无需改动
$para_token = array(
		"service" => "alipay.wap.trade.create.direct",
		"partner" => trim($alipay_config['partner']),
		"sec_id" => trim($alipay_config['sign_type']),
		"format"	=> $format,
		"v"	=> $v,
		"req_id"	=> $req_id,
		"req_data"	=> $req_data,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestHttp($para_token);

//URLDECODE返回的信息
$html_text = urldecode($html_text);

//解析远程模拟提交后返回的信息
$para_html_text = $alipaySubmit->parseResponse($html_text);

//获取request_token
$request_token = $para_html_text['request_token'];


/**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/

//业务详细
$req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
//必填

//构造要请求的参数数组，无需改动
$parameter = array(
		"service" => "alipay.wap.auth.authAndExecute",
		"partner" => trim($alipay_config['partner']),
		"sec_id" => trim($alipay_config['sign_type']),
		"format"	=> $format,
		"v"	=> $v,
		"req_id"	=> $req_id,
		"req_data"	=> $req_data,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter, 'get', '确认');
echo $html_text;
?>
</body>
</html>