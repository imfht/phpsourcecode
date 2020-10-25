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

class Cappay extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();
	private $_fieldsList = array();

	public function __construct()
	{
		$this->name = 'cappay';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.0';
		$this->author = 'MileBiz';
		parent::__construct();

        $this->_errors = array();
		$this->page = basename(__FILE__, '.php');
	    $this->displayName = $this->l('cappay');
	    $this->description = $this->l('Accepts payments with cappay.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		$this->_fieldsList = array(
			'cappay_account' => 
				array(
					'name' =>$this->l('cappay_account'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'cappay_key' => 
				array(
					'name' =>$this->l('cappay_key'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'cappay_currency' => 
				array(
					'name' =>$this->l('cappay_currency'),
					'validate'=>'isString',
					'type'=>'select',
					'range' => array('0'=>$this->l('RMB'),'1'=>$this->l('USD')),
					'value'=>'1'
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
        $v_rcvname   = trim($this->_fieldsList['cappay_account']['value']);
        $m_orderid   = $cart->id;
        $v_amount    = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
        $v_moneytype = trim($this->_fieldsList['cappay_currency']['value']);;
        $v_url       = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/pay_return.php';
        $m_ocomment  = '欢迎使用首信易支付';
        $v_ymd       = date('Ymd',time());

          /*易支付平台*/
        $MD5Key     = $this->_fieldsList['cappay_key']['value'];     //<--支付密钥--> 注:此处密钥必须与商家后台里的密钥一致
        $v_oid      = "$v_ymd-$v_rcvname-$m_orderid";
        $sourcedata = $v_moneytype.$v_ymd.$v_amount.$v_rcvname.$v_oid.$v_rcvname.$v_url;
        $result     = $this->hmac_md5($MD5Key,$sourcedata);
        $def_url  = '<form method=post action="http://pay.beijing.com.cn/prs/user_payment.checkit" target="_blank">';
        $def_url .= "<input type= 'hidden' name = 'v_mid'     value= '".$v_rcvname."'>";     //商户编号
        $def_url .= "<input type= 'hidden' name = 'v_oid'     value= '".$v_oid."'>";         //订单编号
        $def_url .= "<input type= 'hidden' name = 'v_rcvname' value= '".$v_rcvname."'>";     //收货人姓名
        $def_url .= "<input type= 'hidden' name = 'v_rcvaddr' value= '".$v_rcvname."'>";     //收货人地址
        $def_url .= "<input type= 'hidden' name = 'v_rcvtel'  value= '".$v_rcvname."'>";     //收货人电话
        $def_url .= "<input type= 'hidden' name = 'v_rcvpost'  value= '".$v_rcvname."'>";    //收货人邮编
        $def_url .= "<input type= 'hidden' name = 'v_amount'   value= '".$v_amount."'>";     //订单总金额
        $def_url .= "<input type= 'hidden' name = 'v_ymd'      value= '".$v_ymd."'>";        //订单产生日期
        $def_url .= "<input type= 'hidden' name = 'v_orderstatus' value ='0'>";              //配货状态
        $def_url .= "<input type= 'hidden' name = 'v_ordername'   value ='".$v_rcvname."'>"; //订货人姓名
        $def_url .= "<input type= 'hidden' name = 'v_moneytype'   value ='".$v_moneytype."'>"; //币种,0为人民币,1为美元
        $def_url .= "<input type= 'hidden' name='v_url' value='".$v_url."'>";             //支付动作完成后返回到该url，支付结果以GET方式发送
        $def_url .= "<input type='hidden' name='v_md5info' value=$result>";              //订单数字指纹
        $def_url .= "<input type='submit' value='" . $this->l('cappay_button') . "'>";

        $def_url .= '</form>';

        return $def_url;
    }

    /**
     * 响应操作
     */

	public function analyzeReturn(&$redirectLink = '')
	{
		$redirectLink = 'order-confirmation.php?';
        $v_tempdate = explode('-', Tools::getValue('v_oid'));

        //接受返回数据验证开始
        //v_md5info验证
        $md5info_paramet = Tools::getValue('v_oid').Tools::getValue('v_pstatus').Tools::getValue('v_pstring').Tools::getValue('v_pmode');
        $md5info_tem     = $this->hmac_md5($this->_fieldsList['cappay_key']['value'],$md5info_paramet);

        //v_md5money验证
        $md5money_paramet = Tools::getValue('v_amount').Tools::getValue('v_moneytype');
        $md5money_tem     = $this->hmac_md5($this->_fieldsList['cappay_key']['value'],$md5money_paramet);
        if ($md5info_tem == Tools::getValue('v_md5info') && $md5money_tem == Tools::getValue('v_md5money'))
        {
            //改变订单状态
            $this->createSuccessedOrder($v_tempdate[2]);

            return true;
         }
         else
         {
            return false;
         }

    }
    function hmac_md5($key, $data)
    {
        if (extension_loaded('mhash'))
        {
            return bin2hex(mhash(MHASH_MD5, $data, $key));
        }

        // RFC 2104 HMAC implementation for php. Hacked by Lance Rushing
        $b = 64;
        if (strlen($key) > $b)
        {
            $key = pack('H*', md5($key));
        }
        $key  = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));

        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack('H*', md5($k_ipad . $data)));
    }
	
	public function createSuccessedOrder($cart_id)
	{
		global $cookie;
		$cart = new Cart((int)$cart_id);
	    if(!$cart->OrderExists()){
			$total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
			$cappay = new Cappay();
			$cappay->validateOrder($cart->id, _PS_OS_PAYMENT_, $total, $cappay->displayName);
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
					<tr><td colspan="2">'.$this->l('Please enter the cappay configuration information').'.<br /><br /></td></tr>';
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

