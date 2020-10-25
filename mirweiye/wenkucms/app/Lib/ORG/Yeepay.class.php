<?php 
class Yeepay{
	
	var $p1_MerId			= "";				#测试使用
	var $merchantKey	= "";		#测试使用
	var $logName	= "payment/yeepay/YeePay_HTML.log";
	#	产品通用接口正式请求地址
	var $reqURL_onLine = "https://www.yeepay.com/app-merchant-proxy/node";
	#	产品通用接口测试请求地址
	//var $reqURL_onLine = "http://tech.yeepay.com:8080/robot/debug.action";
		
	#	商户接收支付成功数据的地址,支付成功后易宝支付会向该地址发送两次成功通知.
	var $p8_Url						= "";	
	# 业务类型
	# 支付请求，固定值"Buy" .	
	var $p0_Cmd = "Buy";
		
	#	送货地址
	# 为"1": 需要用户将送货地址留在易宝支付系统;为"0": 不需要，默认为 "0".
	var $p9_SAF = "0";

	private $feetype = '';
	
	private $fee = '';
	public function __construct($data){
		$this->p1_MerId = $data['account'];
		$this->merchantKey = $data['key'];
		$this->seller_email = $data['merchant'];
		$this->p8_Url = HOST.U('/Payment/yeepay_return_url');
		$this->mainname = C('sysconfig.site_name');
		$this->feetype = $data['feetype'];
		$this->fee = $data['fee'];
	}
	
	public function _return_url(){
		#	只有支付成功时易宝支付才会通知商户.
		##支付成功回调有两次，都会通知到在线支付请求参数中的p8_Url上：浏览器重定向;服务器点对点通讯.

		#	解析返回参数.
		$return = $this->getCallBackValue($r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType,$hmac);

		#	判断返回签名是否正确（True/False）
		$bRet = $this->CheckHmac($r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType,$hmac);
		#	以上代码和变量不需要修改.
		$result = array(
			'sn'=>$r6_Order,
			'cope'=>$this->delFee($r3_Amt),
		);
		#	校验码正确.
		if($bRet){
			if($r1_Code=="1"){
				
			#	需要比较返回的金额与商家数据库中订单的金额是否相等，只有相等的情况下才认为是交易成功.
			#	并且需要对返回的处理进行事务控制，进行记录的排它性处理，在接收到支付结果通知后，判断是否进行过业务逻辑处理，不要重复进行业务逻辑处理，防止对同一条交易重复发货的情况发生.      	  	
				
				if($r9_BType=="1"){
					return $result;
				}elseif($r9_BType=="2"){
					#如果需要应答机制则必须回写流,以success开头,大小写不敏感.
					$result['ret'] =  "success";
					return $result;		 
				}
			}
			
		}else{
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
		//必填参数
		$p2_Order = $data['sn'];		//请与贵网站订单系统中的唯一订单号匹配
		$p5_Pid   = '';	//订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
		$p7_Pdesc = '';	//订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
		$p3_Amt   = $this->addFee($data['cope']);	//订单总金额，显示在支付宝收银台里的“应付总额”里

		#	交易币种,固定值"CNY".
		$p4_Cur						= "CNY";

		#	商品种类
		$p6_Pcat					= "";

		#	商户扩展信息
		##商户可以任意填写1K 的字符串,支付成功时将原样返回.												
		$pa_MP						= "";

		#	支付通道编码
		##默认为""，到易宝支付网关.若不需显示易宝支付的页面，直接跳转到各银行、神州行支付、骏网一卡通等支付页面，该字段可依照附录:银行列表设置参数值.
		$pd_FrpId					= "";

		#	应答机制
		##默认为"1": 需要应答机制;
		$pr_NeedResponse	= "1";

		#调用签名函数生成签名串
		$hmac = $this->getReqHmacString($p2_Order,$p3_Amt,$p4_Cur,$p5_Pid,$p6_Pcat,$p7_Pdesc,$this->p8_Url,$pa_MP,$pd_FrpId,$pr_NeedResponse);

		$redata = array(
			//'reqURL_onLine'=>$this->reqURL_onLine,
			'p0_Cmd'=>$this->p0_Cmd,
			'p1_MerId'=>$this->p1_MerId,
			'p2_Order'=>$p2_Order,
			'p3_Amt'=>$p3_Amt,
			'p4_Cur'=>$p4_Cur,
			'p5_Pid'=>$p5_Pid,
			'p6_Pcat'=>$p6_Pcat,
			'p7_Pdesc'=>$p7_Pdesc,
			'p8_Url'=>$this->p8_Url,
			'p9_SAF'=>$this->p9_SAF,
			'pa_MP'=>$pa_MP,
			'pd_FrpId'=>$pd_FrpId,
			'pr_NeedResponse'=>$pr_NeedResponse,
			'hmac'=>$hmac,
		);


		$sHtml = $this->buildForm($redata);
		
		return $sHtml;
	}
	
	function buildForm($para) {		
		$sHtml = "<form id='paysubmit' action='".$this->reqURL_onLine."' method='POST' target='_blank'>";
		while (list ($key, $val) = each ($para)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml = $sHtml."</form>";
		return $sHtml;
	}

	
		#签名函数生成签名串
	function getReqHmacString($p2_Order,$p3_Amt,$p4_Cur,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pd_FrpId,$pr_NeedResponse)
	{
			
		#进行签名处理，一定按照文档中标明的签名顺序进行
	  $sbOld = "";
	  #加入业务类型
	  $sbOld = $sbOld.$this->p0_Cmd;
	  #加入商户编号
	  $sbOld = $sbOld.$this->p1_MerId;
	  #加入商户订单号
	  $sbOld = $sbOld.$p2_Order;     
	  #加入支付金额
	  $sbOld = $sbOld.$p3_Amt;
	  #加入交易币种
	  $sbOld = $sbOld.$p4_Cur;
	  #加入商品名称
	  $sbOld = $sbOld.$p5_Pid;
	  #加入商品分类
	  $sbOld = $sbOld.$p6_Pcat;
	  #加入商品描述
	  $sbOld = $sbOld.$p7_Pdesc;
	  #加入商户接收支付成功数据的地址
	  $sbOld = $sbOld.$p8_Url;
	  #加入送货地址标识
	  $sbOld = $sbOld.$this->p9_SAF;
	  #加入商户扩展信息
	  $sbOld = $sbOld.$pa_MP;
	  #加入支付通道编码
	  $sbOld = $sbOld.$pd_FrpId;
	  #加入是否需要应答机制
	  $sbOld = $sbOld.$pr_NeedResponse;
		$this->logstr($p2_Order,$sbOld,$this->HmacMd5($sbOld,$this->merchantKey));
	  return $this->HmacMd5($sbOld,$this->merchantKey);
	  
	} 

	function getCallbackHmacString($r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType)
	{
	  
	  
		#取得加密前的字符串
		$sbOld = "";
		#加入商家ID
		$sbOld = $sbOld.$this->p1_MerId;
		#加入消息类型
		$sbOld = $sbOld.$r0_Cmd;
		#加入业务返回码
		$sbOld = $sbOld.$r1_Code;
		#加入交易ID
		$sbOld = $sbOld.$r2_TrxId;
		#加入交易金额
		$sbOld = $sbOld.$r3_Amt;
		#加入货币单位
		$sbOld = $sbOld.$r4_Cur;
		#加入产品Id
		$sbOld = $sbOld.$r5_Pid;
		#加入订单ID
		$sbOld = $sbOld.$r6_Order;
		#加入用户ID
		$sbOld = $sbOld.$r7_Uid;
		#加入商家扩展信息
		$sbOld = $sbOld.$r8_MP;
		#加入交易结果返回类型
		$sbOld = $sbOld.$r9_BType;

		$this->logstr($r6_Order,$sbOld,$this->HmacMd5($sbOld,$this->merchantKey));
		return $this->HmacMd5($sbOld,$this->merchantKey);

	}


	#	取得返回串中的所有参数
	function getCallBackValue(&$r0_Cmd,&$r1_Code,&$r2_TrxId,&$r3_Amt,&$r4_Cur,&$r5_Pid,&$r6_Order,&$r7_Uid,&$r8_MP,&$r9_BType,&$hmac)
	{  
		$r0_Cmd		= $_REQUEST['r0_Cmd'];
		$r1_Code	= $_REQUEST['r1_Code'];
		$r2_TrxId	= $_REQUEST['r2_TrxId'];
		$r3_Amt		= $_REQUEST['r3_Amt'];
		$r4_Cur		= $_REQUEST['r4_Cur'];
		$r5_Pid		= $_REQUEST['r5_Pid'];
		$r6_Order	= $_REQUEST['r6_Order'];
		$r7_Uid		= $_REQUEST['r7_Uid'];
		$r8_MP		= $_REQUEST['r8_MP'];
		$r9_BType	= $_REQUEST['r9_BType']; 
		$hmac			= $_REQUEST['hmac'];
		
		return null;
	}

	function CheckHmac($r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType,$hmac)
	{
		if($hmac==$this->getCallbackHmacString($r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType))
			return true;
		else
			return false;
	}
			
	  
	function HmacMd5($data,$key)
	{
		
	// RFC 2104 HMAC implementation for php.
	// Creates an md5 HMAC.
	// Eliminates the need to install mhash to compute a HMAC
	// Hacked by Lance Rushing(NOTE: Hacked means written)

	//需要配置环境支持iconv，否则中文参数不能正常处理
	$key = iconv("GB2312","UTF-8",$key);
	$data = iconv("GB2312","UTF-8",$data);

	$b = 64; // byte length for md5
	if (strlen($key) > $b) {
	$key = pack("H*",md5($key));
	}
	$key = str_pad($key, $b, chr(0x00));
	$ipad = str_pad('', $b, chr(0x36));
	$opad = str_pad('', $b, chr(0x5c));
	$k_ipad = $key ^ $ipad ;
	$k_opad = $key ^ $opad;

	return md5($k_opad . pack("H*",md5($k_ipad . $data)));
	}

	function logstr($orderid,$str,$hmac)
	{
	$james=fopen($this->logName,"a+");
	fwrite($james,"\r\n".date("Y-m-d H:i:s")."|orderid[".$orderid."]|str[".$str."]|hmac[".$hmac."]");
	fclose($james);
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