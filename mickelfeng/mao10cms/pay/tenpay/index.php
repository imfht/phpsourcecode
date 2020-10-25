<?php
/*
Template Name: 订单（tenpay）
*/
?>
<?php
$curDateTime = date("YmdHis");
$randNum = rand(1000, 9999);
$qqout_trade_no = mc_user_id() . $curDateTime . $randNum;
$body = $_REQUEST["WIDbody"];
$qqname = mc_option('site_name').'订单';

		$now = strtotime("now");
		$cart = M('action')->where("user_id='".mc_user_id()."' AND action_key='cart'")->getField('id',true);
		if($cart) {
			$action['date'] = $now;
			M('action')->where("user_id='".mc_user_id()."' AND action_key='cart'")->save($action);
			M('action')->where("user_id='".mc_user_id()."' AND action_key='address_pending'")->delete();
			M('action')->where("user_id='".mc_user_id()."' AND action_key='trade_pending'")->delete();
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
		} else {
			$this->error('购物车里没有任何商品！');
		};

        //付款金额
        $qqprice = mc_total();
        
$qqtransport_fee = 0.00; //$_REQUEST["WIDlogistics_fee"];
$total_fee = $qqprice+$qqtransport_fee;
$bank_type = $_REQUEST["bank_type"];

   require_once ("classes/RequestHandler.class.php");
   require_once ("tenpay_config.php");
  $curDateTime = date("YmdHis");
 
  
  //date_default_timezone_set(PRC);
		$strDate = date("Ymd");
		$strTime = date("His");
		
		//4位随机数
		$randNum = rand(1000, 9999);
		
		//10位序列号,可以自行调整。
		$strReq = $strTime . $randNum;
		 /* 商家的定单号 */
  	$mch_vno = $curDateTime . $randNum;
?>
 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>财付通付款通道</TITLE>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
</HEAD>
<BODY topMargin=0>

                        <script language="javascript">
	function payFrm()
	{
		if (directFrm.order_no.value=="")
		{
			alert("提醒：请填写订单编号；如果无特定的订单编号，请采用默认编号！（刷新一下页面就可以了）");
			directFrm.order_no.focus();
			return false;
		}
		if (directFrm.product_name.value=="")
		{
			alert("提醒：请填写商品名称(付款项目)！");
			directFrm.product_name.focus();
			return false;
		}
		if (directFrm.order_price.value=="")
		{
			alert("提醒：请填写订单的交易金额！");
			directFrm.order_price.focus();
			return false;
		}
		
		if (directFrm.remarkexplain.value=="")
		{
			alert("提醒：请填写您的简要说明！");
			directFrm.remarkexplain.focus();
			return false;
		}
		if (directFrm.remarkexplain.value.length>31)
		{
			alert("提醒：收货地址超出规定的字数,请重新输入");
  			event.returnValue=false;   
  			return   false;   
		}
		
		return true;
	}
  </script>
<style>
#ddqr {width: 400px; padding: 30px; border: 1px solid #000; margin: 100px auto 50px;font-size: 12px;}
#ddqr h2 {font-size: 14px; margin-bottom: 20px;}
#ddqr p {margin: 0 0 10px;}
#ddqr textarea {width: 398px; border: 1px solid #e3e3e3; background: #f9f9f9; margin-bottom: 10px;}
#submit {width:100px; height: 40px; background: #000; color: #fff; text-align: center; _line-height: 40px; font-size: 14px; border: 1px solid #000; margin: 0 auto; display: block; cursor: pointer; }
#submit:hover {background: #fff; color: #000;}
</style>
<form action='<?php bloginfo('template_directory'); ?>/tenpay/tenpay.php' method='post' name='directFrm' onSubmit="return payFrm();">
<input type="hidden" name="order_no" value="<?php echo $qqout_trade_no ?>">
<!--input type="hidden" name="order_no" value="<?php echo $mch_vno ?>"-->
<input name="product_name" type="hidden" value="王子部落商品">
<input type="hidden" name="order_price" value="<?php echo $total_fee; ?>"> 
<input type="hidden" name="trade_mode" value="1">
<!--input type="radio" name="trade_mode" value="2" checked="true">
<input type="radio" name="trade_mode" value="3"-->
<div id="ddqr">
<h2>订单确认</h2>
<p>商品：<?php echo $qqname; ?></p>
<!--textarea name="remarkexplain" cols="60" rows="10"><?php echo $qqdizhi; ?></textarea-->
<input type="hidden" name="bank_type_value" value="<?php echo $bank_type; ?>"  id="bank_type_value">
<input name="submit" type="submit" id="submit" value="提交" />
</div>
</form>
</body>
</html>
