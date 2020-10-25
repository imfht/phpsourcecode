<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

if (!defined('_MB_VERSION_'))
	exit;

class Shenzhou extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();
	private $_fieldsList = array();

	public function __construct()
	{
		$this->name = 'shenzhou';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.0';
		$this->author = 'MileBiz';
		parent::__construct();

        $this->_errors = array();
		$this->page = basename(__FILE__, '.php');
	    $this->displayName = $this->l('shenzhou');
	    $this->description = $this->l('Accepts payments with shenzhou.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		$this->_fieldsList = array(
			'shenzhou_account' => 
				array(
					'name' =>$this->l('shenzhou_account'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'shenzhou_key' => 
				array(
					'name' =>$this->l('shenzhou_key'),
					'validate'=>'isString',
					'type'=>'text',
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
        $merchant_acctid    = trim($this->_fieldsList['shenzhou_account']['value']);                 //快钱神州行账号 不可空
        $key                = trim($this->_fieldsList['shenzhou_key']['value']);                     //密钥 不可空
        $input_charset      = 1;                                               //字符集 默认1=utf-8
        $bg_url             = '';
        $page_url           = $GLOBALS['ecs']->url() . 'respond.php';
        $version            = 'v2.0';
        $language           = 1;
        $sign_type          = 1;                                               //签名类型 不可空 固定值 1:md5
        $payer_name         = '';
        $payer_contact_type = '';
        $payer_contact      = '';
        $order_id           = $cart->id;                                //商户订单号 不可空
        $order_amount       = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', '')) * 100;                    //商户订单金额 不可空
        $pay_type           = '00';                                            //支付方式 不可空
        $card_number        = '';
        $card_pwd           = '';
        $full_amount_flag   = '0';
        $order_time         = date('YmdHis', date('Ymd', (time() - date('Z'))));        //商户订单提交时间 不可空 14位
        $product_name       = '';
        $product_num        = '';
        $product_id         = '';
        $product_desc       = '';
        $ext1               = $cart->id;
        $ext2               = 'milebiz';

        /* 生成加密签名串 请务必按照如下顺序和规则组成加密串！*/
        $signmsgval = '';
        $signmsgval = $this->append_param($signmsgval, "inputCharset", $input_charset);
        $signmsgval = $this->append_param($signmsgval, "bgUrl", $bg_url);
        $signmsgval = $this->append_param($signmsgval, "pageUrl", $page_url);
        $signmsgval = $this->append_param($signmsgval, "version", $version);
        $signmsgval = $this->append_param($signmsgval, "language", $language);
        $signmsgval = $this->append_param($signmsgval, "signType", $sign_type);
        $signmsgval = $this->append_param($signmsgval, "merchantAcctId", $merchant_acctid);
        $signmsgval = $this->append_param($signmsgval, "payerName", urlencode($payer_name));
        $signmsgval = $this->append_param($signmsgval, "payerContactType", $payer_contact_type);
        $signmsgval = $this->append_param($signmsgval, "payerContact", $payer_contact);
        $signmsgval = $this->append_param($signmsgval, "orderId", $order_id);
        $signmsgval = $this->append_param($signmsgval, "orderAmount", $order_amount);
        $signmsgval = $this->append_param($signmsgval, "payType", $pay_type);
        $signmsgval = $this->append_param($signmsgval, "cardNumber", $card_number);
        $signmsgval = $this->append_param($signmsgval, "cardPwd", $card_pwd);
        $signmsgval = $this->append_param($signmsgval, "fullAmountFlag", $full_amount_flag);
        $signmsgval = $this->append_param($signmsgval, "orderTime", $order_time);
        $signmsgval = $this->append_param($signmsgval, "productName", urlencode($product_name));
        $signmsgval = $this->append_param($signmsgval, "productNum", $product_num);
        $signmsgval = $this->append_param($signmsgval, "productId", $product_id);
        $signmsgval = $this->append_param($signmsgval, "productDesc", urlencode($product_desc));
        $signmsgval = $this->append_param($signmsgval, "ext1", urlencode($ext1));
        $signmsgval = $this->append_param($signmsgval, "ext2", urlencode($ext2));
        $signmsgval = $this->append_param($signmsgval, "key", $key);
        $sign_msg    = strtoupper(md5($signmsgval));    //安全校验域 不可空

        $def_url  = '<div style="text-align:center"><form name="kqPay" style="text-align:center;" method="post"'.
        'action="https://www.99bill.com/szxgateway/recvMerchantInfoAction.htm" target="_blank">';
        $def_url .= "<input type= 'hidden' name='inputCharset' value='" . $input_charset . "' />";
        $def_url .= "<input type='hidden' name='bgUrl' value='" . $bg_url . "' />";
        $def_url .= "<input type='hidden' name='pageUrl' value='" . $page_url . "' />";
        $def_url .= "<input type='hidden' name='version' value='" . $version . "' />";
        $def_url .= "<input type='hidden' name='language' value='" . $language . "' />";
        $def_url .= "<input type='hidden' name='signType' value='" . $sign_type . "' />";
        $def_url .= "<input type='hidden' name='merchantAcctId' value='" . $merchant_acctid . "' />";
        $def_url .= "<input type='hidden' name='payerName' value='" . $payer_name . "' />";
        $def_url .= "<input type='hidden' name='payerContactType' value='" . $payer_contact_type . "' />";
        $def_url .= "<input type='hidden' name='payerContact' value='" . $payer_contact . "' />";
        $def_url .= "<input type='hidden' name='orderId' value='" . $order_id . "' />";
        $def_url .= "<input type='hidden' name='orderAmount' value='" . $order_amount . "' />";
        $def_url .= "<input type='hidden' name='payType' value='" . $pay_type . "' />";
        $def_url .= "<input type='hidden' name='cardNumber' value='" . $card_number . "' />";
        $def_url .= "<input type='hidden' name='cardPwd' value='" . $card_pwd . "' />";
        $def_url .= "<input type='hidden' name='fullAmountFlag' value='" .$full_amount_flag ."' />";
        $def_url .= "<input type='hidden' name='orderTime' value='" . $order_time . "' />";
        $def_url .= "<input type='hidden' name='productName' value='" . urlencode($product_name) . "' />";
        $def_url .= "<input type='hidden' name='productNum' value='" . $product_num . "' />";
        $def_url .= "<input type='hidden' name='productId' value='" . $product_id . "' />";
        $def_url .= "<input type='hidden' name='productDesc' value='" . urlencode($product_desc) . "' />";
        $def_url .= "<input type='hidden' name='ext1' value='" . urlencode($ext1) . "' />";
        $def_url .= "<input type='hidden' name='ext2' value='" . urlencode($ext2) . "' />";
        $def_url .= "<input type='hidden' name='signMsg' value='" . $sign_msg ."' />";
        $def_url .= "<input type='submit' name='submit' value='".$this->l('pay_button')."' />";
        $def_url .= "</form></div><br />";

        return $def_url;
    }

    /**
     * 响应操作
     */
	public function analyzeReturn(&$redirectLink = '')
	{
		$redirectLink = 'order-confirmation.php?';
        $merchant_acctid     = $this->_fieldsList['shenzhou_account']['value'];                 //收款帐号 不可空
        $key                 = $this->_fieldsList['shenzhou_key']['value'];
        $get_merchant_acctid = trim(Tools::getValue('merchantAcctId'));     //接收的收款帐号
        $pay_result          = trim(Tools::getValue('payResult'));
        $version             = trim(Tools::getValue('version'));
        $language            = trim(Tools::getValue('language'));
        $sign_type           = trim(Tools::getValue('signType'));
        $pay_type            = trim(Tools::getValue('payType'));            //20代表神州行卡密直接支付；22代表快钱账户神州行余额支付
        $card_umber          = trim(Tools::getValue('cardNumber'));
        $card_pwd            = trim(Tools::getValue('cardPwd'));
        $order_id            = trim(Tools::getValue('orderId'));            //订单号
        $order_time          = trim(Tools::getValue('orderTime'));
        $order_amount        = trim(Tools::getValue('orderAmount'));
        $deal_id             = trim(Tools::getValue('dealId'));             //获取该交易在快钱的交易号
        $ext1                = trim(Tools::getValue('ext1'));
        $ext2                = trim(Tools::getValue('ext2'));
        $pay_amount          = trim(Tools::getValue('payAmount'));          //获取实际支付金额
        $bill_order_time     = trim(Tools::getValue('billOrderTime'));
        $pay_result          = trim(Tools::getValue('payResult'));         //10代表支付成功； 11代表支付失败
        $sign_type           = trim(Tools::getValue('signType'));
        $sign_msg            = trim(Tools::getValue('signMsg'));

        //生成加密串。必须保持如下顺序。
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "merchantAcctId", $merchant_acctid);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "version", $version);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "language", $language);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "payType", $pay_type);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "cardNumber", $card_number);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "cardPwd", $card_pwd);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "orderId", $order_id);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "orderAmount", $order_amount);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "dealId", $deal_id);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "orderTime", $order_time);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "ext1", $ext1);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "ext2", $ext2);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "payAmount", $pay_amount);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "billOrderTime", $bill_order_time);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "payResult", $pay_result);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "signType", $sign_type);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval, "key", $key);
        $merchant_signmsg    = md5($merchant_signmsgval);

        //首先对获得的商户号进行比对
        if ($get_merchant_acctid != $merchant_acctid)
        {
            //'商户号错误';
            return false;
        }

        if (strtoupper($sign_msg) == strtoupper($merchant_signmsg))
        {
            if ($pay_result == 10)  //有成功支付的结果返回10
            {
                $this->createSuccessedOrder($ext1);

                return true;
            }
            elseif ($pay_result == 11  && $pay_amount > 0)
            {
                $sql = "SELECT order_amount FROM " . $GLOBALS['ecs']->table('order_info') ."WHERE order_id = '$order_id'";
                $get_order_amount = $GLOBALS['db']->getOne($sql);
                if ($get_order_amount == $pay_amount && $get_order_amount == $order_amount) //检查订单金额、实际支付金额和订单是否相等
                {
                    $this->createSuccessedOrder($ext1);

                    return true;
                }
                elseif ($get_order_amount == $order_amount && $pay_amount > 0) //订单金额相等 实际支付金额 > 0的情况
                {
                    $surplus_amount = $get_order_amount - $pay_amount;        //计算订单剩余金额
                    $sql = 'UPDATE' . $GLOBALS['ecs']->table('order_info') . "SET `money_paid` = (money_paid  + '$pay_amount')," .
                        " order_amount = (order_amount - '$pay_amount') WHERE order_id = '$order_id'";
                    $result = $GLOBALS['db']->query($sql);
                    $sql = 'UPDATE' . $GLOBALS['ecs']->table('order_info') . "SET `order_status` ='" . OS_CONFIRMED . "' WHERE order_id = '$orderId'";
                    $result = $GLOBALS['db']->query($sql);
                    //$this->createSuccessedOrder($orderId);
                    //'订单金额小于0';
                    return false;
                }
                else
                {
                    //'订单金额不相等';
                    return false;
                }
            }
            else
            {
                //'实际支付金额不能小于0';
                return false;
            }
        }
        else
        {
            //'签名校对错误';
            return false;
        }
    }

    /**
     * 将变量值不为空的参数组成字符串
     * @param   string   $strs  参数字符串
     * @param   string   $key   参数键名
     * @param   string   $val   参数键对应值
    */
    function append_param($strs,$key,$val)
    {
        if($strs != "")
        {
            if($val != "")
            {
                $strs .= '&' . $key . '=' . $val;
            }
        }
        else
        {
            if($val != "")
            {
                $strs = $key . '=' . $val;
            }
        }

        return $strs;
    }

	
	public function createSuccessedOrder($cart_id)
	{
		global $cookie;
		$cart = new Cart((int)$cart_id);
	    if(!$cart->OrderExists()){
			$total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
			$shenzhou = new Shenzhou();
			$shenzhou->validateOrder($cart->id, _PS_OS_PAYMENT_, $total, $shenzhou->displayName);
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
					<tr><td colspan="2">'.$this->l('Please enter the shenzhou configuration information').'.<br /><br /></td></tr>';
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

