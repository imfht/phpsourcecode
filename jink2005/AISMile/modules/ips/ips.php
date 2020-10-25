<?php
/*
* 
* 2007-2011 Power by 米乐商城 
* 
*/ 

if (!defined('_MB_VERSION_'))
	exit;

class Ips extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();
	private $_fieldsList = array();

	public function __construct()
	{
		$this->name = 'ips';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.0';
		$this->author = 'MileBiz';
		parent::__construct();

        $this->_errors = array();
		$this->page = basename(__FILE__, '.php');
	    $this->displayName = $this->l('ips');
	    $this->description = $this->l('Accepts payments with ips.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		$this->_fieldsList = array(
			'ips_account' => 
				array(
					'name' =>$this->l('ips_account'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'ips_key' => 
				array(
					'name' =>$this->l('ips_key'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'ips_currency' => 
				array(
					'name' =>$this->l('ips_currency'),
					'validate'=>'isString',
					'type'=>'select',
					'range' => array(
						'01' => $this->l('借记卡'),
						'02' => $this->l('信用卡'),
						'04' => $this->l('IPS账户支付'),
						'08' => $this->l('I币支付'),
						'16' => $this->l('电话支付')),
					'value'=>'01'
				),
			'ips_lang' => 
				array(
					'name' =>$this->l('ips_lang'),
					'validate'=>'isString',
					'type'=>'select',
					'range' => array(
						'GB'   => $this->l('GB中文'),
						'EN'   => $this->l('英语'),
						'BIG5' => $this->l('BIG5中文'),
						'JP'   => $this->l('JP'),
						'FR'   => $this->l('FR')),
					'value'=>'GB'
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
        $billstr    = date('His', time());
        $datestr    = date('Ymd', time());
        $mer_code   = $this->_fieldsList['ips_account']['value'];
        $billno     = str_pad($cart->id, 10, '0', STR_PAD_LEFT) . $billstr;
        $amount     = sprintf("%0.02f", floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', '')));
        $strcert    = $this->_fieldsList['ips_key']['value'];
        $strcontent = $billno . $amount . $datestr . 'RMB' . $strcert; // 签名验证串 //
        $signmd5    = MD5($strcontent);

        $def_url  = '<br /><form style="text-align:center;" action="https://pay.ips.com.cn/ipayment.aspx" method="post" target="_blank">';
        $def_url .= "<input type='hidden' name='Mer_code' value='" . $mer_code . "'>\n";
        $def_url .= "<input type='hidden' name='Billno' value='" . $billno . "'>\n";
        $def_url .= "<input type='hidden' name='Gateway_type' value='" . $this->_fieldsList['ips_currency']['value'] . "'>\n";
        $def_url .= "<input type='hidden' name='Currency_Type'  value='RMB'>\n";
        $def_url .= "<input type='hidden' name='Lang'  value='" . $this->_fieldsList['ips_lang']['value'] . "'>\n";
        $def_url .= "<input type='hidden' name='Amount'  value='" . $amount . "'>\n";
        $def_url .= "<input type='hidden' name='Date' value='" . $datestr . "'>\n";
        $def_url .= "<input type='hidden' name='DispAmount' value='" . $amount . "'>\n";
        $def_url .= "<input type='hidden' name='OrderEncodeType' value='2'>\n";
        $def_url .= "<input type='hidden' name='RetEncodeType' value='12'>\n";
        $def_url .= "<input type='hidden' name='Merchanturl' value='" . (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/pay_return.php' . "'>\n";
        $def_url .= "<input type='hidden' name='SignMD5' value='" . $signmd5 . "'>\n";
        $def_url .= "<input type='submit' value='" . $this->l('pay_button') . "'>";
        $def_url .= "</form><br />";

        return $def_url;
    }

	public function analyzeReturn(&$redirectLink = '')
	{
		$redirectLink = 'order-confirmation.php?';
        $billno        = $_GET['billno'];
        $amount        = $_GET['amount'];
        $mydate        = $_GET['date'];
        $succ          = $_GET['succ'];
        $msg           = $_GET['msg'];
        $ipsbillno     = $_GET['ipsbillno'];
        $retEncodeType = $_GET['retencodetype'];
        $currency_type = $_GET['Currency_type'];
        $signature     = $_GET['signature'];
        $order_sn      = intval(substr($billno, 0, 10));

        if ($succ == 'Y')
        {
            $content = $billno . $amount . $mydate . $succ . $ipsbillno . $currency_type;
            $cert = $this->_fieldsList['ips_key']['value'];
            $signature_1ocal = md5($content . $cert);

            if ($signature_1ocal == $signature)
            {
                if (!$this->_checkReturnMoney($order_sn, $amount))
                {
                   return false;
                }
                $this->createSuccessedOrder($order_sn);

                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
	
	public function createSuccessedOrder($cart_id)
	{
		global $cookie;
		$cart = new Cart((int)$cart_id);
	    if(!$cart->OrderExists()){
			$total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
			$ips = new Ips();
			$ips->validateOrder($cart->id, _PS_OS_PAYMENT_, $total, $ips->displayName);
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
					<tr><td colspan="2">'.$this->l('Please enter the ips configuration information').'.<br /><br /></td></tr>';
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

