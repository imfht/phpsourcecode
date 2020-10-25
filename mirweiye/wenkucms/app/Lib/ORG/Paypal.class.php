<?php 
class Paypal{
	
	private $_config		= array();
	
	private $feetype = '';
	
	private $fee = '';
	
	public function __construct($data){
		$this->set_config($data);
		$this->feetype = $data['feetype'];
		$this->fee = $data['fee'];
	}

	public function set_config($_config){
		require_once "payment/Paypal/utility.php";
		$this->_config = array(
				'default_dev_central'=>'jvfnet',
				'default_env'=>'sandbox',
				'default_email_address'=>$_config['merchant'],
				'default_identity_token'=>$_config['key'],
				'default_ewp_cert_path'=>PUBLIC_PATH.'paypal/my-pubcert.pem',
				'default_ewp_private_key_path'=>PUBLIC_PATH.'paypal/my-prvkey.pem',
				'default_ewp_private_key_pwd'=>$_config['key'],
				'default_cert_id'=>$_config['account'],
				'paypal_cert_path'=>PUBLIC_PATH.'paypal/paypal_cert_pem.txt',
				'paypal_ipn_log'=>PUBLIC_PATH.'paypal/paypal-ipn.log',
				
		);
		
		//页面跳转同步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		//return_url的域名不能写成http://localhost/create_direct_pay_by_user_php_utf8/return_url.php ，否则会导致return_url执行无效
		$this->_config['return_url']   = HOST.U('/payment/alipay_return_url');
		
		//服务器异步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		$this->_config['notify_url']   = HOST.U('/payment/alipay_notify_url');
		
		return $this->_config;
	}
	
	public function _notify_url(){
		$logStr = "";
		$logFd = fopen($this->_config['paypal_ipn_log'], "a");
		fwrite($logFd, "****************************************************************************************************\n");
		
		if(array_key_exists("txn_id", $_POST)) {
			$logStr = "Received IPN,  TX ID : ".htmlspecialchars($_POST["txn_id"]);
			fwrite($logFd, strftime("%d %b %Y %H:%M:%S ")."[IPNListner.php] $logStr\n");
		} else {
			$logStr = "IPN Listner recieved an HTTP request with out a Transaction ID.";
			fwrite($logFd, strftime("%d %b %Y %H:%M:%S ")."[IPNListner.php] $logStr\n");
			fclose($logFd);
			exit;
		}
		
		$tmpAr = array_merge($_POST, array("cmd" => "_notify-validate"));
		$postFieldsAr = array();
		foreach ($tmpAr as $name => $value) {
			$postFieldsAr[] = "$name=$value";
		}
		$logStr = "Sending IPN values:\n".implode("\n", $postFieldsAr);
		fwrite($logFd, strftime("%d %b %Y %H:%M:%S ")."[IPNListner.php] $logStr\n");
		
		$ppResponseAr = Utils::PPHttpPost("https://www.".$this->_config['default_env'].".paypal.com/cgi-bin/webscr", implode("&", $postFieldsAr), false);
		if(!$ppResponseAr["status"]) {
			fwrite($logFd, "--------------------\n");
			$logStr = "IPN Listner recieved an Error:\n";
			if(0 !== $ppResponseAr["error_no"]) {
				$logStr .= "Error ".$ppResponseAr["error_no"].": ";
			}
			$logStr .= $ppResponseAr["error_msg"];
			fwrite($logFd, strftime("%d %b %Y %H:%M:%S ")."[IPNListner.php] $logStr\n");
			fclose($logFd);
			return false;
		}
		
		fwrite($logFd, "--------------------\n");
		$logStr = "IPN Post Response:\n".$ppResponseAr["httpResponse"];
		fwrite($logFd, strftime("%d %b %Y %H:%M:%S ")."[IPNListner.php] $logStr\n");
		
		fclose($logFd);
		return false;
	}
	
	public function _return_url(){
		if(!array_key_exists("tx", $_GET)) {
			Utils::PPError("PDT received an HTTP GET request without a transaction ID.", 0);
			return false;
		}
		
		$url = "https://www.".$this->_config['default_env'].".paypal.com/cgi-bin/webscr";
		$postFields =	"cmd=".urlencode("_notify-synch").
						"&tx=".urlencode(htmlspecialchars($_GET["tx"])).
						"&at=".urlencode($this->_config['default_identity_token']);
		$ppResponseAr = Utils::PPHttpPost($url, $postFields, true);
		
		if(!$ppResponseAr["status"]) {
			Utils::PPError($ppResponseAr["error_msg"], $ppResponseAr["error_no"]);
			return false;
		}
		$httpParsedResponseAr = $ppResponseAr["httpParsedResponseAr"];
		$result = array(
			'sn'=>htmlspecialchars($_GET["tx"]),
			'cope'=>$this->delFee(urldecode(htmlspecialchars($_GET["amt"]))),
		);
		return $result;
	}
	
	/*$data 为数组
	 * RE
	 * ['sn']定单号
	 * ['amount'] 订单价格
	 * 
	 * PA
	 * ['ordersn']定单号
	 * ['goodsname']商品名称
	 * ['remark']商品描述
	 * ['orderprice'] 订单价格
	 * 
	 * $type 为订单类型 RE充值 PA支付
	 * */
	public function _payto($data,$payBlank = ''){
		require_once "payment/Paypal/EWPServices.php";
		/**************************请求参数**************************/
		$payBlank = $this->getBlank($payBlank);
		//必填参数//
		$buttonParams = array(	"cmd"			=> "_xclick",
				"business" 		=> $this->_config['default_email_address'],
				"cert_id"		=> $this->_config['default_cert_id'],
				"charset"		=> "UTF-8",
				"item_name"		=> htmlspecialchars($data['sn']),
				"item_number"	=> htmlspecialchars($data['sn']),
				"amount"		=> htmlspecialchars($this->addFee($data['cope'])),
				"currency_code"	=> htmlspecialchars('USD'),
				"return"		=> htmlspecialchars($this->_config['return_url']),
				"cancel_return"	=> htmlspecialchars(HOST.U('/Member/buyOrderList')),
				"notify_url"	=> htmlspecialchars($this->_config['notify_url']),
				"custom"		=> "PayPal EWP Sample");
		
		$envURL = "https://www.".$this->_config['default_env'].".paypal.com";
		
		$buttonReturn = EWPServices::encryptButton(	$buttonParams,
				$this->_config['default_ewp_cert_path'],
				$this->_config['default_ewp_private_key_path'],
				$this->_config['default_ewp_private_key_pwd'],
				$this->_config['paypal_cert_path'],
				$envURL);
		if(!$buttonReturn["status"]) {
			exit;
		}
		$html_text = $buttonReturn["encryptedButton"];
		return $html_text;

	}
	
	public function addFee($amount){
		//比例收取
		if($this->feetype){
			return $amount + $amount * $this->fee;
		}else{
			//定额
			return $amount + $this->fee;
		}
	}
	
	public function delFee($amount){
		//比例收取
		if($this->feetype){
			return $amount - $amount * $this->fee;
		}else{
			//定额
			return $amount - $this->fee;
		}
	}
	
	public function getBlank($payBlank){
		$bank = array(
			/*'ICBC-B2B'=>'ICBCBTB',//中国工商银行（B2B）
			'ABC-B2B'=>'ABCBTB',// 中国农业银行（B2B）
			'CCB-B2B'=>'CCBBTB', //中国建设银行（B2B）
			'SPDB-B2B'=>'SPDBB2B', //上海浦东发展银行（B2B）
			'BOC'=>'BOCB2C', //中国银行
			'ICBC'=>'ICBCB2C', //中国工商银行
			'CMB'=>'CMB', //招商银行
			'CCB'=>'CCB', //中国建设银行
			'ABC'=>'ABC', //中国农业银行
			'SPDB'=>'SPDB', //上海浦东发展银行
			'CIB'=>'CIB', //兴业银行
			'GDB'=>'GDB', //广东发展银行
			'SDB'=>'SDB', //深圳发展银行
			'CMBC'=>'CMBC', //中国民生银行
			'BCM'=>'COMM',//交通银行
			'CITIC'=>'CITIC', //中信银行
			'HZB'=>'HZCBB2C', //杭州银行
			'CEB'=>'CEBBANK', //中国光大银行
			'SHB'=>'SHBANK', //上海银行
			'NBB'=>'NBBANK', //宁波银行
			'PAB'=>'SPABANK', //平安银行
			'BJNB'=>'BJRCB', //北京农村商业银行
			'FDB'=>'FDB', //富滇银行
			'PSBC'=>'POSTGC',// 中国邮政储蓄银行
			'BJB'=>'BJBANK', //北京银行
			'VISA'=>'abc1003', //visa
			'MASTER'=>'abc1004', //master
		
			//纯借记卡
			'CMB-DEBIT'=>'CMB-DEBIT',// 招商银行
			'CCB-DEBIT'=>'CCB-DEBIT', //中国建设银行
			'ICBC-DEBIT'=>'ICBC-DEBIT', //中国工商银行
			'COMM-DEBIT'=>'COMM-DEBIT', //交通银行
			'GDB-DEBIT'=>'GDB-DEBIT', //广东发展银行
			'BOC-DEBIT'=>'BOC-DEBIT', //中国银行
			'CEB-DEBIT'=>'CEB-DEBIT',//中国光大银行
			'SPDB-DEBIT'=>'SPDB-DEBIT', //上海浦东发展银行
			'PSBC-DEBIT'=>'PSBC-DEBIT', //中国邮政储蓄银行
			'SHNB-DEBIT'=>'SHRCB', //上海农商银行
			'WZB-DEBIT'=>'WZCBB2C-DEBIT',//温州银行*/
		);
		return $bank['payBlank'];
	}
	
	
}
?>