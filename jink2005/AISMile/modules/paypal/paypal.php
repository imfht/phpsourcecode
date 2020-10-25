<?php
/*
* 
* 2007-2011 Power by 米乐商城 
* 
*/ 

if (!defined('_MB_VERSION_'))
	exit;

class Paypal extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();
	private $_fieldsList = array();

	public function __construct()
	{
		$this->name = 'paypal';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.0';
		$this->author = 'MileBiz';
		parent::__construct();

        $this->_errors = array();
		$this->page = basename(__FILE__, '.php');
	    $this->displayName = $this->l('paypal');
	    $this->description = $this->l('Accepts payments with paypal.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		$this->_fieldsList = array(
			'paypal_account' => 
				array(
					'name' =>$this->l('paypal_account'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'paypal_currency' => 
				array(
					'name' =>$this->l('paypal_currency'),
					'validate'=>'isString',
					'type'=>'select',
					'range' => array(
						'AUD' => $this->l('澳元'),
						'CAD' => $this->l('加元'),
						'EUR' => $this->l('欧元'),
						'GBP' => $this->l('英镑'),
						'JPY' => $this->l('日元'),
						'USD' => $this->l('美元'),
						'HKD' => $this->l('港元')),
					'value'=>'USD'
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
        $data_order_id      = $cart->id;
        $data_amount        = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
        $data_return_url    = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/pay_return.php';
        $data_pay_account   = $this->_fieldsList['paypal_account']['value'];
        $currency_code      = $this->_fieldsList['paypal_currency']['value'];
        $data_notify_url    = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/pay_notify.php';
        $cancel_return      = $GLOBALS['ecs']->url();

        $def_url  = '<br /><form style="text-align:center;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">' .   // 不能省略
            "<input type='hidden' name='cmd' value='_xclick'>" .                             // 不能省略
            "<input type='hidden' name='business' value='$data_pay_account'>" .                 // 贝宝帐号
            "<input type='hidden' name='item_name' value='$order[order_sn]'>" .                 // payment for
            "<input type='hidden' name='amount' value='$data_amount'>" .                        // 订单金额
            "<input type='hidden' name='currency_code' value='$currency_code'>" .            // 货币
            "<input type='hidden' name='return' value='$data_return_url'>" .                    // 付款后页面
            "<input type='hidden' name='invoice' value='$data_order_id'>" .                      // 订单号
            "<input type='hidden' name='charset' value='utf-8'>" .                              // 字符集
            "<input type='hidden' name='no_shipping' value='1'>" .                              // 不要求客户提供收货地址
            "<input type='hidden' name='no_note' value=''>" .                                  // 付款说明
            "<input type='hidden' name='notify_url' value='$data_notify_url'>" .
            "<input type='hidden' name='rm' value='2'>" .
            "<input type='hidden' name='cancel_return' value='$cancel_return'>" .
            "<input type='submit' value='" . $this->l('paypal_button') . "'>" .                      // 按钮
            "</form><br />";

        return $def_url;
    }

    /**
     * 响应操作
     */
	public function analyzeReturn(&$redirectLink = '')
	{
		$redirectLink = 'order-confirmation.php?';
        $merchant_id    = $this->_fieldsList['paypal_account']['value'];               ///获取商户编号

        // read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value)
        {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }

        // post back to PayPal system to validate
        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) ."\r\n\r\n";
        $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

        // assign posted variables to local variables
        $item_name = $_POST['item_name'];
        $item_number = $_POST['item_number'];
        $payment_status = $_POST['payment_status'];
        $payment_amount = $_POST['mc_gross'];
        $payment_currency = $_POST['mc_currency'];
        $txn_id = $_POST['txn_id'];
        $receiver_email = $_POST['receiver_email'];
        $payer_email = $_POST['payer_email'];
        $order_sn = $_POST['invoice'];
        $memo = !empty($_POST['memo']) ? $_POST['memo'] : '';
        $action_note = $txn_id . '（' . $this->l('paypal_txn_id') . '）' . $memo;

        if (!$fp)
        {
            fclose($fp);

            return false;
        }
        else
        {
            fputs($fp, $header . $req);
            while (!feof($fp))
            {
                $res = fgets($fp, 1024);
                if (strcmp($res, 'VERIFIED') == 0)
                {
                    // check the payment_status is Completed
                    if ($payment_status != 'Completed' && $payment_status != 'Pending')
                    {
                        fclose($fp);

                        return false;
                    }

                    // check that receiver_email is your Primary PayPal email
                    if ($receiver_email != $merchant_id)
                    {
                        fclose($fp);

                        return false;
                    }

                    // check that payment_amount/payment_currency are correct
                    $sql = "SELECT order_amount FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE log_id = '$order_sn'";
                    if ($GLOBALS['db']->getOne($sql) != $payment_amount)
                    {
                        fclose($fp);

                        return false;
                    }
                    if ($this->_fieldsList['paypal_currency']['value'] != $payment_currency)
                    {
                        fclose($fp);

                        return false;
                    }

                    // process payment
                    $this->createSuccessedOrder($order_sn);
                    fclose($fp);

                    return true;
                }
                elseif (strcmp($res, 'INVALID') == 0)
                {
                    // log for manual investigation
                    fclose($fp);

                    return false;
                }
            }
        }
    }

	
	public function createSuccessedOrder($cart_id)
	{
		global $cookie;
		$cart = new Cart((int)$cart_id);
	    if(!$cart->OrderExists()){
			$total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
			$paypal = new Paypal();
			$paypal->validateOrder($cart->id, _PS_OS_PAYMENT_, $total, $paypal->displayName);
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
					<tr><td colspan="2">'.$this->l('Please enter the paypal configuration information').'.<br /><br /></td></tr>';
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

