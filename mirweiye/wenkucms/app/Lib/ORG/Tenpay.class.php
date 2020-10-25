<?php 
class Tenpay{
	
 	/* 商户号 */
	private $bargainor_id = "";

	/* 密钥 */
	private $key = "";
	
	/* 返回处理地址 */
	private $return_url = "";
	
	private $reqHandler;
	private $resHandler;
	
	
	public function __construct($data){
		require_once ("payment/tenpay/PayRequestHandler.class.php");
		require_once ("payment/tenpay/PayResponseHandler.class.php");
		$this->bargainor_id = $data['account'];
		$this->key = $data['key'];
		
		$reqHandler = new PayRequestHandler();
		$reqHandler->init();
		$reqHandler->setKey($this->key);
		$resHandler = new PayResponseHandler();
		$resHandler->setKey($this->key);
		
		$this->return_url = HOST.U('Payment/tenpay_return_url');
		$this->reqHandler = $reqHandler;
		$this->resHandler = $resHandler;
	}
	
	public function _return_url(){
		//判断签名
		if($this->resHandler->isTenpaySign()) {			
			//支付结果
			$pay_result = $this->resHandler->getParameter("pay_result");
			$result = array(
				'sn'=>$this->resHandler->getParameter("sp_billno"),
				'cash'=>$this->resHandler->getParameter("total_fee"),
			);
			if( "0" == $pay_result ) {
				return $result;			
			} else {
				//当做不成功处理
				return false;
			}
			
		} else {
			return false;
		}
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
	public function _payto($data,$paybank){
		/*以下参数是需要通过下单时的订单数据传入进来获得*/
		
		//date_default_timezone_set(PRC);
		$strDate = date("Ymd");
		$strTime = date("His");
		
		//4位随机数
		$randNum = rand(1000, 9999);
		
		//10位序列号,可以自行调整。
		$strReq = $strTime . $randNum;

		/* 财付通交易单号，规则为：10位商户号+8位时间（YYYYmmdd)+10位流水号 */
		$transaction_id = $this->bargainor_id . $strDate . $strReq;
		
		//必填参数
		$sp_billno = $data['sn'];		//请与贵网站订单系统中的唯一订单号匹配
		$total_fee    = $data['cash'];	//订单总金额，显示在支付宝收银台里的“应付总额”里
		
		$this->reqHandler->setParameter("bargainor_id", $this->bargainor_id);			//商户号
		$this->reqHandler->setParameter("sp_billno", $sp_billno);					//商户订单号
		$this->reqHandler->setParameter("transaction_id", $transaction_id);		//财付通交易单号
		$this->reqHandler->setParameter("total_fee", $total_fee);					//商品总金额,以分为单位
		$this->reqHandler->setParameter("return_url", $this->return_url);				//返回处理地址
		$this->reqHandler->setParameter("desc", $transaction_id);	//商品名称
		
		//用户ip,测试环境时不要加这个ip参数，正式环境再加此参数
		$this->reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);
		$sHtmlText = $this->reqHandler->getRequestURL();
		return $sHtmlText;
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