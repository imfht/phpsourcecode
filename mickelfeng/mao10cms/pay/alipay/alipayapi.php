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

/**************************请求参数**************************/

        //支付类型
        $payment_type = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url = mc_option('site_url').'/pay/alipay_notify_url.php';
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        //页面跳转同步通知页面路径
        $return_url = mc_option('site_url').'/pay/alipay_return_url.php';
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        //卖家支付宝帐户
        $seller_email = mc_option('alipay_seller');
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
        //订单描述
        $body = $_POST['WIDbody'];
        //商品展示地址
        $show_url = $_POST['WIDshow_url'];
        //需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html
        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数
        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1


/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
		"service" => "create_direct_pay_by_user",
		"partner" => trim($alipay_config['partner']),
		"payment_type"	=> $payment_type,
		"notify_url"	=> $notify_url,
		"return_url"	=> $return_url,
		"seller_email"	=> $seller_email,
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"total_fee"	=> $total_fee,
		"body"	=> $body,
		"show_url"	=> $show_url,
		"anti_phishing_key"	=> $anti_phishing_key,
		"exter_invoke_ip"	=> $exter_invoke_ip,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
echo $html_text;

?>
</body>
</html>