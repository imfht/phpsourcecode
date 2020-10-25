<?php
/*
* 
* 2007-2011 Power by 米乐商城 
* 
*/ 

if (!defined('_MB_VERSION_'))
	exit;

class TenpayC2c extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();
	private $_fieldsList = array();

	public function __construct()
	{
		$this->name = 'tenpayc2c';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.0';
		$this->author = 'MileBiz';
		parent::__construct();

        $this->_errors = array();
		$this->page = basename(__FILE__, '.php');
	    $this->displayName = $this->l('tenpayc2c');
	    $this->description = $this->l('Accepts payments with tenpayc2c.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		$this->_fieldsList = array(
			'tenpay_account' => 
				array(
					'name' =>$this->l('tenpay_account'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'tenpay_key' => 
				array(
					'name' =>$this->l('tenpay_key'),
					'validate'=>'isString',
					'type'=>'text',
					'value'=>''
				),
			'tenpay_type' => 
				array(
					'name' =>$this->l('tenpay_type'),
					'validate'=>'isString',
					'type'=>'select',
					'range' => array(
						'2'  => $this->l('虚拟物品交易'),
						'1'  => $this->l('实物商品交易')),
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
        /* 版本号 */
        $version = '2';

        /* 任务代码，定值：12 */
        $cmdno = '12';

        /* 编码标准 */
        $encode_type = 2;

        /* 平台提供者,代理商的财付通账号 */
        $chnid = $this->_fieldsList['tenpay_account']['value'];

        /* 收款方财付通账号 */
        $seller = $this->_fieldsList['tenpay_account']['value'];

        /* 商品名称 */
        if (!empty($cart->id))
        {
            //$mch_name = get_goods_name_by_id($cart->id);
            $mch_name = $cart->id;
        }
        else
        {
            $mch_name = $this->l('account_voucher');
        }

        /* 总金额 */
        $mch_price = floatval(floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''))) * 100;

        /* 物流配送说明 */
        $transport_desc = '';
        $transport_fee = '';

        /* 交易说明 */
        $mch_desc = $this->l('shop_order_sn') . $cart->id;
        $need_buyerinfo = '2' ;

        /* 交易类型：2、虚拟交易，1、实物交易 */
        $mch_type = $this->_fieldsList['tenpay_type']['value'];

        /* 获得订单的流水号，补零到10位 */
        $mch_vno = $cart->id;

        /* 返回的路径 */
        $mch_returl = return_url('tenpayc2c');
        $show_url   = return_url('tenpayc2c');
        $attach = '';

        /* 数字签名 */
        $sign_text = "chnid=" . $chnid . "&cmdno=" . $cmdno . "&encode_type=" . $encode_type . "&mch_desc=" . $mch_desc . "&mch_name=" . $mch_name . "&mch_price=" . $mch_price ."&mch_returl=" . $mch_returl . "&mch_type=" . $mch_type . "&mch_vno=" . $mch_vno . "&need_buyerinfo=" . $need_buyerinfo ."&seller=" . $seller . "&show_url=" . $show_url . "&version=" . $version . "&key=" . $this->_fieldsList['tenpay_key']['value'];

        $sign =md5($sign_text);

        /* 交易参数 */
        $parameter = array(
            'attach'            => $attach,
            'chnid'             => $chnid,
            'cmdno'             => $cmdno,                     // 业务代码, 财付通支付支付接口填  1
            'encode_type'       => $encode_type,                //编码标准
            'mch_desc'          => $mch_desc,
            'mch_name'          => $mch_name,
            'mch_price'         => $mch_price,                  // 订单金额
            'mch_returl'        => $mch_returl,                 // 接收财付通返回结果的URL
            'mch_type'          => $mch_type,                   //交易类型
            'mch_vno'           => $mch_vno,             // 交易号(订单号)，由商户网站产生(建议顺序累加)
            'need_buyerinfo'    => $need_buyerinfo,             //是否需要在财付通填定物流信息
            'seller'            => $seller,  // 商家的财付通商户号
            'show_url'          => $show_url,
            'transport_desc'    => $transport_desc,
            'transport_fee'     => $transport_fee,
            'version'           => $version,                    //版本号 2
            'sign'              => $sign,                       // MD5签名
            'sys_id'            => '542554970'                  // C账号 不参与签名
        );

        $button  = '<br /><form style="text-align:center;" action="https://www.tenpay.com/cgi-bin/med/show_opentrans.cgi " target="_blank" style="margin:0px;padding:0px" >';

        foreach ($parameter AS $key=>$val)
        {
            $button  .= "<input type='hidden' name='$key' value='$val' />";
        }

        $button  .= '<input type="image" src="'. $GLOBALS['ecs']->url() .'images/tenpayc2c.jpg" value="' .$this->l('pay_button'). '" /></form><br />';

        return $button;
    }

    /**
     * 响应操作
     */
	public function analyzeReturn(&$redirectLink = '')
	{
		$redirectLink = 'order-confirmation.php?';
        /*取返回参数*/
        $cmd_no         = $_GET['cmdno'];
        $retcode        = $_GET['retcode'];
        $status         = $_GET['status'];
        $seller         = $_GET['seller'];
        $total_fee      = $_GET['total_fee'];
        $trade_price    = $_GET['trade_price'];
        $transport_fee  = $_GET['transport_fee'];
        $buyer_id       = $_GET['buyer_id'];
        $chnid          = $_GET['chnid'];
        $cft_tid        = $_GET['cft_tid'];
        $mch_vno        = $_GET['mch_vno'];
        $attach         = !empty($_GET['attach']) ? $_GET['attach'] : '';
        $version        = $_GET['version'];
        $sign           = $_GET['sign'];

        $log_id     = get_order_id_by_sn($mch_vno);
        //$log_id = str_replace($attach, '', $mch_vno); //取得支付的log_id

        /* 如果$retcode大于0则表示支付失败 */
        if ($retcode > 0)
        {
            //echo '操作失败';
            return false;
        }

        /* 检查支付的金额是否相符 */
        if (!$this->_checkReturnMoney($log_id, $total_fee / 100))
        {
            //echo '金额不相等';
            return false;
        }

        /* 检查数字签名是否正确 */
        $sign_text = "buyer_id=" . $buyer_id . "&cft_tid=" . $cft_tid . "&chnid=" . $chnid . "&cmdno=" . $cmd_no . "&mch_vno=" . $mch_vno . "&retcode=" . $retcode . "&seller=" .$seller . "&status=" . $status . "&total_fee=" . $total_fee . "&trade_price=" . $trade_price . "&transport_fee=" . $transport_fee . "&version=" . $version . "&key=" . $this->_fieldsList['tenpay_key']['value'];
        $sign_md5 = strtoupper(md5($sign_text));
        if ($sign_md5 != $sign)
        {
            //echo '签名错误';
            return false;
        }
        elseif ($status = 3)
        {
            /* 改变订单状态为已付款 */
            $this->createSuccessedOrder($log_id);
            return true;
        }
        else
        {
            //为止error
            return false;
        }
    }

	
	public function createSuccessedOrder($cart_id)
	{
		global $cookie;
		$cart = new Cart((int)$cart_id);
	    if(!$cart->OrderExists()){
			$total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
			$tenpayc2c = new TenpayC2c();
			$tenpayc2c->validateOrder($cart->id, _PS_OS_PAYMENT_, $total, $tenpayc2c->displayName);
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
					<tr><td colspan="2">'.$this->l('Please enter the tenpayc2c configuration information').'.<br /><br /></td></tr>';
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

