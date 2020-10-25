<?php
/*
* 
* 2007-2011 Power by 米乐商城 
* 
*/ 

if (!defined('_MB_VERSION_'))
	exit;

class PaypalEc extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();
	private $_fieldsList = array();

	public function __construct()
	{
		$this->name = 'paypalec';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.0';
		$this->author = 'MileBiz';
		parent::__construct();

        $this->_errors = array();
		$this->page = basename(__FILE__, '.php');
	    $this->displayName = $this->l('paypalec');
	    $this->description = $this->l('Accepts payments with paypalec.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		$this->_fieldsList = array(
			'paypal_ec_username' => 
				array(
					'name' =>$this->l('paypal_ec_username'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'paypal_ec_password' => 
				array(
					'name' =>$this->l('paypal_ec_password'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'paypal_ec_signature' => 
				array(
					'name' =>$this->l('paypal_ec_signature'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'paypal_ec_currency' => 
				array(
					'name' =>$this->l('paypal_ec_currency'),
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

        $token = '';
        $serverName = $_SERVER['SERVER_NAME'];
        $serverPort = $_SERVER['SERVER_PORT'];
        $url=dirname('http://'.$serverName.':'.$serverPort.$_SERVER['REQUEST_URI']);
        $paymentAmount=floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
        $currencyCodeType=$this->_fieldsList['paypal_ec_currency']['value'];
        $paymentType='Sale';
        $data_order_id      = $cart->id;

        $_SESSION['paypal_username']=$this->_fieldsList['paypal_ec_username']['value'];
        $_SESSION['paypal_password']=$this->_fieldsList['paypal_ec_password']['value'];
        $_SESSION['paypal_signature']=$this->_fieldsList['paypal_ec_signature']['value'];

        $returnURL =urlencode($url.'/respond.php?code=paypal_ec&currencyCodeType='.$currencyCodeType.'&paymentType='.$paymentType.'&paymentAmount='.$paymentAmount.'&invoice='.$data_order_id);
        $cancelURL =urlencode("$url/SetExpressCheckout.php?paymentType=$paymentType" );

        $nvpstr="&Amt=".$paymentAmount."&PAYMENTACTION=".$paymentType."&ReturnUrl=".$returnURL."&CANCELURL=".$cancelURL ."&CURRENCYCODE=".$currencyCodeType ."&ButtonSource=MILEBIZ_cart_MB_M2";

        $resArray=$this->hash_call("SetExpressCheckout",$nvpstr);

        $_SESSION['reshash']=$resArray;
        if(isset($resArray["ACK"]))
        {
            $ack = strtoupper($resArray["ACK"]);
        }
        
        if (isset($resArray["TOKEN"]))
        {
            $token = urldecode($resArray["TOKEN"]);
        }            
            $payPalURL = PAYPAL_URL.$token;
            $button = '<div style="text-align:center"><input type="button" onclick="window.open(\''.$payPalURL. '\')" value="' .$this->l('pay_button'). '"/></div>';

        return $button;
    }

    /**
     * 响应操作
     */
	public function analyzeReturn(&$redirectLink = '')
	{
		$redirectLink = 'order-confirmation.php?';
        $order_sn = Tools::getValue('invoice');
        $token =urlencode( Tools::getValue('token'));
        $nvpstr="&TOKEN=".$token;
        $resArray=$this->hash_call("GetExpressCheckoutDetails",$nvpstr);
        $_SESSION['reshash']=$resArray;
        $ack = strtoupper($resArray["ACK"]);
        if($ack=="SUCCESS")
        {
            $_SESSION['token']=Tools::getValue('token');
            $_SESSION['payer_id'] = Tools::getValue('PayerID');

            $_SESSION['paymentAmount']=Tools::getValue('paymentAmount');
            $_SESSION['currCodeType']=Tools::getValue('currencyCodeType');
            $_SESSION['paymentType']=Tools::getValue('paymentType');

            $resArray=$_SESSION['reshash'];
            $token =urlencode( $_SESSION['token']);

            $paymentAmount =urlencode ($_SESSION['paymentAmount']);
            $paymentType = urlencode($_SESSION['paymentType']);
            $currCodeType = urlencode($_SESSION['currCodeType']);
            $payerID = urlencode($_SESSION['payer_id']);
            $serverName = urlencode($_SERVER['SERVER_NAME']);

            $nvpstr='&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currCodeType.'&IPADDRESS='.$serverName ;

            $resArray=$this->hash_call("DoExpressCheckoutPayment",$nvpstr);
            
            $ack = strtoupper($resArray["ACK"]);
            if($ack=="SUCCESS")
            {
                /* 改变订单状态 */
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

    function hash_call($methodName,$nvpStr)
    {
        global $API_Endpoint;
        $version='53.0';
        $API_UserName=$_SESSION['paypal_username'];
        $API_Password=$_SESSION['paypal_password'];
        $API_Signature=$_SESSION['paypal_signature'];
        $nvp_Header;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);

        if(USE_PROXY)
        {
            curl_setopt ($ch, CURLOPT_PROXY, PROXY_HOST.":".PROXY_PORT);
        }

        $nvpreq="METHOD=".urlencode($methodName)."&VERSION=".urlencode($version)."&PWD=".urlencode($API_Password)."&USER=".urlencode($API_UserName)."&SIGNATURE=".urlencode($API_Signature).$nvpStr;

        curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);

        $response = curl_exec($ch);

        $nvpResArray=$this->deformatNVP($response);
        
        $nvpReqArray=$this->deformatNVP($nvpreq);

        $_SESSION['nvpReqArray']=$nvpReqArray;

        if (curl_errno($ch))
        {
            $_SESSION['curl_error_no']=curl_errno($ch) ;
            $_SESSION['curl_error_msg']=curl_error($ch);
        }
        else
        {
            curl_close($ch);
        }

        return $nvpResArray;
    }


    function deformatNVP($nvpstr)
    {

        $intial=0;
        $nvpArray = array();

        while(strlen($nvpstr))
        {
            $keypos= strpos($nvpstr,'=');
            $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
            $keyval=substr($nvpstr,$intial,$keypos);
            $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
            $nvpArray[urldecode($keyval)] =urldecode( $valval);
            $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
        }

        return $nvpArray;
    }


	
	public function createSuccessedOrder($cart_id)
	{
		global $cookie;
		$cart = new Cart((int)$cart_id);
	    if(!$cart->OrderExists()){
			$total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
			$paypalec = new PaypalEc();
			$paypalec->validateOrder($cart->id, _PS_OS_PAYMENT_, $total, $paypalec->displayName);
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
					<tr><td colspan="2">'.$this->l('Please enter the paypalec configuration information').'.<br /><br /></td></tr>';
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

