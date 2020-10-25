<?php
/*
* 
* 2007-2011 Power by 米乐商城 
* 
*/ 

if (!defined('_MB_VERSION_'))
	exit;

class Cncard extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();
	private $_fieldsList = array();

	public function __construct()
	{
		$this->name = 'cncard';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.0';
		$this->author = 'MileBiz';
		parent::__construct();

        $this->_errors = array();
		$this->page = basename(__FILE__, '.php');
	    $this->displayName = $this->l('cncard');
	    $this->description = $this->l('Accepts payments with cncard.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		$this->_fieldsList = array(
			'c_mid' => 
				array(
					'name' =>$this->l('c_mid'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'c_pass' => 
				array(
					'name' =>$this->l('c_pass'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'c_memo1' => 
				array(
					'name' =>$this->l('c_memo1'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>'milebiz'
				),
			'c_moneytype' => 
				array(
					'name' =>$this->l('c_moneytype'),
					'validate'=>'isString',
					'type'=>'select',
					'range' => array('0'=>$this->l('RMB')),
					'value'=>'0'
				),
			'c_language' => 
				array(
					'name' =>$this->l('c_language'),
					'validate'=>'isString',
					'type'=>'select',
					'range' => array('0'=>$this->l('Chinese'),'1'=>$this->l('English')),
					'value'=>'0'
				),
			'c_paygate' => 
				array(
					'name' =>$this->l('c_paygate'),
					'validate'=>'isString',
					'type'=>'select',
					'range' => array('0' =>$this->l('云网支付'),
									'1' =>$this->l('中国招商银行'),
									'3' =>$this->l('中国工商银行网上银行'),
									'31' =>$this->l('中国工商银行手机银行(短信)'),
									'2000' =>$this->l('中国建行签约客户(全国)'),
									'2' =>$this->l('北京建设银行'),
									'2021' =>$this->l('上海建设银行'),
									'2022' =>$this->l('天津建设银行'),
									'2023' =>$this->l('重庆建设银行'),
									'2024' =>$this->l('辽宁建设银行'),
									'2025' =>$this->l('江苏建设银行'),
									'2027' =>$this->l('湖北建设银行'),
									'2028' =>$this->l('四川建设银行'),
									'2029' =>$this->l('陕西建设银行'),
									'20371' =>$this->l('河南建设银行(只限签约用户)'),
									'20411' =>$this->l('大连建设银行'),
									'20431' =>$this->l('吉林建设银行'),
									'20451' =>$this->l('黑龙江建设银行'),
									'20512' =>$this->l('苏州建设银行'),
									'20532' =>$this->l('青岛建设银行'),
									'20571' =>$this->l('浙江建设银行'),
									'20574' =>$this->l('宁波建设银行(只限签约用户)'),
									'20591' =>$this->l('福建建设银行(只限签约用户)'),
									'20592' =>$this->l('厦门建设银行'),
									'20731' =>$this->l('湖南建设银行(只限签约用户)'),
									'20755' =>$this->l('深圳建设银行'),
									'20771' =>$this->l('广西建设银行'),
									'20791' =>$this->l('江西建设银行'),
									'20991' =>$this->l('新疆建设银行'),
									'20314' =>$this->l('河北建设银行(只限签约用户)'),
									'20351' =>$this->l('山西建设银行(只限签约用户)'),
									'20471' =>$this->l('内蒙古建设银行(只限签约用户)'),
									'20851' =>$this->l('贵州建设银行(只限签约用户)'),
									'20871' =>$this->l('云南建设银行(只限签约用户)'),
									'20898' =>$this->l('海南建设银行(只限签约用户)'),
									'20931' =>$this->l('甘肃建设银行(只限签约用户)'),
									'20951' =>$this->l('宁夏建设银行(只限签约用户)'),
									'20531' =>$this->l('山东建设银行'),
									'86' =>$this->l('中国农业银行'),
									'901' =>$this->l('中国民生银行(普通用户)'),
									'902' =>$this->l('中国民生银行(签约用户)'),
									'5' =>$this->l('中国光大银行'),
									'5' =>$this->l('广东发展银行'),
									'5' =>$this->l('中信实业银行'),
									'94' =>$this->l('深圳发展银行'),
									'42' =>$this->l('福建兴业银行'),
									'47' =>$this->l('中国交通银行'),
									'87' =>$this->l('国际卡'),
									'100' =>$this->l('云网会员卡拉'),
									'5' =>$this->l('广东银联网上安全支付平台'),
									'5' =>$this->l('广东地区其它银行'),
									'7' =>$this->l('厦门金卡中心支付网关'),
									'5' =>$this->l('中国银行(广东，深圳除外)'),
									'7' =>$this->l('中国银行(厦门借记卡)'),
									'5' =>$this->l('华夏银行(广东)'),
									'5' =>$this->l('福建兴业银行(广州)'),
									'5' =>$this->l('广州市商业银行(广州)'),
									'5' =>$this->l('广州农村信用合作社(广州)'),
									'5' =>$this->l('上海浦东发展银行(广州)')),
					'value'=>''
				)
		);

		foreach($this->_fieldsList as $field => $detail)
		{
			if(Configuration::get($this->_getKey($field)))
			{
				$this->_fieldsList[$field]['value'] = Configuration::get($this->_getKey($field));
			}
		}
	}
	
    public function doPayment()
	{
		global $cart,$cookie;
        $c_mid      = trim($this->_fieldsList['c_mid']['value']);  //商户编号，在申请商户成功后即可获得，可以在申请商户成功的邮件中获取该编号
        $c_order    = $cart->id;       //商户网站依照订单号规则生成的订单号，不能重复
        $c_name     = "";                       //商户订单中的收货人姓名
        $c_address  = "";                       //商户订单中的收货人地址
        $c_tel      = "";                       //商户订单中的收货人电话
        $c_post     = "";                       //商户订单中的收货人邮编
        $c_email    = "";                       //商户订单中的收货人Email
        $c_orderamount = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));        //商户订单总金额
        $c_ymd      = date('Ymd', (time() - date('Z')));

        $c_moneytype= $this->_fieldsList['c_moneytype']['value'];          //支付币种，0为人民币
        $c_retflag  = "1";                              //商户订单支付成功后是否需要返回商户指定的文件，0：不用返回 1：需要返回
        $c_paygate  = empty($this->_fieldsList['c_paygate']['value']) ? '' : trim($this->_fieldsList['c_paygate']['value']); //如果在商户网站选择银行则设置该值，具体值可参见《云网支付@网技术接口手册》附录一；如果来云网支付@网选择银行此项为空值。
        $c_returl   = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/pay_return.php'; //如果c_retflag为1时，该地址代表商户接收云网支付结果通知的页面，请提交完整文件名(对应范例文件：GetPayNotify.php)
        $c_memo1    = abs(crc32(trim($this->_fieldsList['c_memo1']['value'])));     //商户需要在支付结果通知中转发的商户参数一
        if (empty($cart->id))
        {
            $c_memo2    = "voucher";                      //商户需要在支付结果通知中转发的商户参数二
        }
        else
        {
            $c_memo2    = '';
        }
        $c_pass     = trim($this->_fieldsList['c_pass']['value']);      //支付密钥，请登录商户管理后台，在帐户信息-基本信息-安全信息中的支付密钥项
        $notifytype = "0";                           //0普通通知方式/1服务器通知方式，空值为普通通知方式
        $c_language = trim($this->_fieldsList['c_language']['value']);  //对启用了国际卡支付时，可使用该值定义消费者在银行支付时的页面语种，值为：0银行页面显示为中文/1银行页面显示为英文

        $srcStr = $c_mid . $c_order . $c_orderamount . $c_ymd . $c_moneytype . $c_retflag . $c_returl . $c_paygate . $c_memo1 . $c_memo2 . $notifytype . $c_language . $c_pass;      //说明：如果您想指定支付方式(c_paygate)的值时，需要先让用户选择支付方式，然后再根据用户选择的结果在这里进行MD5加密，也就是说，此时，本页面应该拆分为两个页面，分为两个步骤完成。

        //--对订单信息进行MD5加密
        //商户对订单信息进行MD5签名后的字符串
        $c_signstr  = md5($srcStr);

         $def_url = '<form name="payForm1" action="https://www.cncard.net/purchase/getorder.asp" method="POST" target="_blank">'.
                    "<input type=\"hidden\" name=\"c_mid\" value=\"$c_mid\" />".
                    "<input type=\"hidden\" name=\"c_order\" value=\"$c_order\" />".
                    "<input type=\"hidden\" name=\"c_name\" value=\"$c_name\" />".
                    "<input type=\"hidden\" name=\"c_address\" value=\"$c_address\" />".
                    "<input type=\"hidden\" name=\"c_tel\" value=\"$c_tel\" />".
                    "<input type=\"hidden\" name=\"c_post\" value=\"$c_post\" />".
                    "<input type=\"hidden\" name=\"c_email\" value=\"$c_email\" />".
                    "<input type=\"hidden\" name=\"c_orderamount\" value=\"$c_orderamount\" />".
                    "<input type=\"hidden\" name=\"c_ymd\" value=\"$c_ymd\" />".
                    "<input type=\"hidden\" name=\"c_moneytype\" value=\"$c_moneytype\" />".
                    "<input type=\"hidden\" name=\"c_retflag\" value=\"$c_retflag\" />".
                    "<input type=\"hidden\" name=\"c_paygate\" value=\"$c_paygate\" />".
                    "<input type=\"hidden\" name=\"c_returl\" value=\"$c_returl\" />".
                    "<input type=\"hidden\" name=\"c_memo1\" value=\"$c_memo1\" />".
                    "<input type=\"hidden\" name=\"c_memo2\" value=\"$c_memo2\" />".
                    "<input type=\"hidden\" name=\"c_language\" value=\"$c_language\" />".
                    "<input type=\"hidden\" name=\"notifytype\" value=\"$notifytype\" />".
                    "<input type=\"hidden\" name=\"c_signstr\" value=\"$c_signstr\" />".
                    "<input type=\"submit\" name=\"submit\" value=\"".$this->l('cncard_button')."\" />".
                    "</form>";

        return $def_url;
    }

    /**
     * 响应操作
     */

	public function analyzeReturn(&$redirectLink = '')
	{
		$redirectLink = 'order-confirmation.php?';
        

        //--获取云网支付网关向商户发送的支付通知信息(以下简称为通知信息)
        $c_mid          = Tools::getValue('c_mid');           //商户编号，在申请商户成功后即可获得，可以在申请商户成功的邮件中获取该编号
        $c_order        = Tools::getValue('c_order');         //商户提供的订单号
        $c_orderamount  = Tools::getValue('c_orderamount');   //商户提供的订单总金额，以元为单位，小数点后保留两位，如：13.05
        $c_ymd          = Tools::getValue('c_ymd');           //商户传输过来的订单产生日期，格式为"yyyymmdd"，如20050102
        $c_transnum     = Tools::getValue('c_transnum');      //云网支付网关提供的该笔订单的交易流水号，供日后查询、核对使用；
        $c_succmark     = Tools::getValue('c_succmark');      //交易成功标志，Y-成功 N-失败
        $c_moneytype    = Tools::getValue('c_moneytype');     //支付币种，0为人民币
        $c_cause        = Tools::getValue('c_cause');         //如果订单支付失败，则该值代表失败原因
        $c_memo1        = Tools::getValue('c_memo1');         //商户提供的需要在支付结果通知中转发的商户参数一
        $c_memo2        = Tools::getValue('c_memo2');         //商户提供的需要在支付结果通知中转发的商户参数二
        $c_signstr      = Tools::getValue('c_signstr');       //云网支付网关对已上信息进行MD5加密后的字符串

        //--校验信息完整性---
        if($c_mid=="" || $c_order=="" || $c_orderamount=="" || $c_ymd=="" || $c_moneytype=="" || $c_transnum=="" || $c_succmark=="" || $c_signstr=="")
        {
            //echo "支付信息有误!";

            return false;
        }

        //--将获得的通知信息拼成字符串，作为准备进行MD5加密的源串，需要注意的是，在拼串时，先后顺序不能改变
        //商户的支付密钥，登录商户管理后台(https://www.cncard.net/admin/)，在管理首页可找到该值
        $c_pass = trim($this->_fieldsList['c_pass']['value']);

        $srcStr = $c_mid . $c_order . $c_orderamount . $c_ymd . $c_transnum . $c_succmark . $c_moneytype . $c_memo1 . $c_memo2 . $c_pass;

        //--对支付通知信息进行MD5加密
        $r_signstr  = md5($srcStr);

        //--校验商户网站对通知信息的MD5加密的结果和云网支付网关提供的MD5加密结果是否一致
        if($r_signstr!=$c_signstr)
        {
            //echo "签名验证失败";

            return false;
        }
        //验证通过后,将订单sn转换为ID 来操作ec订单表
        if ($c_memo2 == 'voucher')
        {
            $c_order = get_order_id_by_sn($c_order, "true");
        }
        else
        {
            $c_order = get_order_id_by_sn($c_order);
        }

        /* 检查支付的金额是否相符 */
        if (!$this->_checkReturnMoney($c_order, $c_orderamount))
        {
            //echo "订单金额不对";

            return false;
        }

        //--校验商户编号
        $MerchantID= trim($this->_fieldsList['c_mid']['value']);   //商户自己的编号
        if($MerchantID!=$c_mid){
            //echo "提交的商户编号有误";

            return false;
        }

        if ($c_memo1 != abs(crc32($this->_fieldsList['c_memo1']['value'])))
        {
            //echo "个性签名不一致";

            //return false;
        }

        //--校验返回的支付结果的格式是否正确
        if($c_succmark!="Y" && $c_succmark!="N")
        {
            //echo "参数提交有误";

            return false;
        }

        //--根据返回的支付结果，商户进行自己的发货等操作
        if($c_succmark="Y")
        {
            //根据商户自己商务规则，进行发货等系列操作

            /* 改变订单状态 */
            $this->createSuccessedOrder($c_order);

            return true;
        }
        else
        {
            //echo $c_cause;

            return false;
        }
    }
	
	public function createSuccessedOrder($cart_id)
	{
		global $cookie;
		$cart = new Cart((int)$cart_id);
	    if(!$cart->OrderExists()){
			$total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
			$cncard = new Cncard();
			$cncard->validateOrder($cart->id, _PS_OS_PAYMENT_, $total, $cncard->displayName);
		}
		$order = new Order((int)($alipay->currentOrder));
		
		return 'id_cart='.$cart_id.'&id_module='.$this->id.'&key='.$order->secure_key;
	}

	public function install()
	{
		/* Install and register on hook */
		if (!parent::install()
			OR !$this->registerHook('payment')
			OR !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}
	
	public function uninstall()
	{
		if(!$this->unregisterHook('payment') || !$this->unregisterHook('paymentReturn'))
			return false;
		return parent::uninstall();
	}
	
	public function hookPayment($params)
	{
		
		if (!$this->active)
			return ;
		$this->smarty->assign('payment_link',$this->doPayment());

		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return ;

		return $this->display(__FILE__, 'payment-return.tpl');
	}
	
	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (!empty($_POST))
		{
			if (!$this->_postValidation())
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error">'. $err .'</div>';
		}
		else
			$this->_html .= '<br />';

		$this->_displayForm();

		return $this->_html;
	}
	
	private function _postValidation()
	{
		if (!isset($_POST['btnSubmit'])) return;
		$validate = new Validate();
		foreach($this->_fieldsList as $field => $detail)
		{
			$method = $detail['validate'];
			if (!method_exists($validate, $method))
				die (Tools::displayError('Validation function not found.').' '.$method);
			if(!call_user_func(array('Validate', $method), Tools::getValue($field)))
				$this->_postErrors[] = $detail['name'].$this->l(' format Incorrect .');
		}
		if(sizeof($this->_postErrors))
			return false;
		
		foreach($this->_fieldsList as $field => $detail)
		{
			$this->_fieldsList[$field]['value'] = Tools::getValue($field);
			Configuration::updateValue($this->_getKey($field), Tools::getValue($field));
		}
		return true;
	}

	private function _displayForm()
	{
		$this->_html .=
		'<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
				<legend><img src="../img/admin/contact.gif" />'.$this->l('Configuration information').'</legend>
				<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
					<tr><td colspan="2">'.$this->l('Please enter the cncard configuration information').'.<br /><br /></td></tr>';
		foreach($this->_fieldsList as $field => $detail){
			$this->_html .= '
			<tr>
				<td width="130" style="height: 35px;">'.$detail['name'].'</td>
				<td>';
				$value = htmlentities(Tools::getValue($field,isset($detail['value'])?$detail['value']:''));
				if($detail['type'] == 'text'){
					$this->_html .= '
					<input type="text" name="'.$field.'" value="'.$value.'" size="40" />
					';
				}
				else if($detail['type'] == 'textarea'){
					$this->_html .= '
					<textarea name="'.$field.'" cols="80" rows="5">'.$value.'</textarea>
					';
				}
				else if($detail['type'] == 'select'){
					$this->_html .= '<select name="'.$field.'">';
					foreach($detail['range'] as $key => $option){
						if($key == $value){
							$this->_html .= '<option value="'.$key.'" selected="selected">'.$option.'</option>';
						}else{
							$this->_html .= '<option value="'.$key.'">'.$option.'</option>';
						}
      				}
      				$this->_html .= '</select>';
				}else{
					$this->_html .= '
						type error!
					';
				}
			$this->_html .= '
				</td>
			</tr>
			';
		}
		$this->_html .= '
					<tr><td colspan="2" align="center"><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
				</table>
		  </fieldset>
		</form>';
	}
	private function _getKey($field)
	{
		return $this->name.'_'.$field;
	}
	
	private function _checkReturnMoney($cart_id,$total_fee)
	{
		$cart = new Cart( $cart_id );
        $total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
        /* 检查支付的金额是否相符 */
        if ($total!== floatval($total_fee))
        {
            return false;
        }
		return true;
	}	
}

