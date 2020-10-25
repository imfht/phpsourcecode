<?php 
class Chinapnr{
	
	var $buyTrans = 'http://mas.chinapnr.com/gar/OnlineBuyTrans.do';
	var $MerPriv = 'http://mas.chinapnr.com/hftest/page/testCmbPay.jsp';
	var $transRespChk = 'http://mas.chinapnr.com/gar/OnlineTransRespChk.do';

	var $key = '';
	var $partner = '';
	var $seller_email = '';
	var $notify_url = '';
	var $return_url = '';
	var $show_url = '';
	var $mainname = '';

	public function __construct($data){
		$this->partner = $data['business'];
		$this->key = $data['key'];
		$this->seller_email = $data['merchant'];
		$this->notify_url = HOST.U('/Payment/chinapnr_notify_url');
		$this->return_url = HOST.U('/Payment/chinapnr_return_url');
		$this->mainname = C('sysconfig.site_name');
		$this->feetype = $data['feetype'];
		$this->fee = $data['fee'];
	}
	
	public function _notify_url(){
		$CmdId 		= trim($_POST['CmdId']);
		$RespCode 		= trim($_POST['RespCode']);
		$UsrId 		= trim($_POST['UsrId']);
		$TrxId 	= trim($_POST['TrxId']);
		$OrdAmt 	= trim($_POST['OrdAmt']);
		$OrdId 	= trim($_POST['OrdId']);
		$MerPriv 	= trim($_POST['MerPriv']);
		$RetType 		= trim($_POST['RetType']);
		$GateId 	= trim($_POST['GateId']);
		$ChkValue 	= trim($_POST['ChkValue']);
		
		require_once("payment/chinapnr/HttpClient.class.php");
		$pageContents=httpClient::quickPost("{$this->transRespChk}?",array(
		'Version'=>"10",
		'CmdId'=>$CmdId,
		'RespCode'=>$RespCode,
		'UsrId'=>$UsrId,
		'TrxId'=>$TrxId,
		'OrdAmt'=>$OrdAmt,
		'OrdId'=>$OrdId,
		'MerPriv'=>$MerPriv,
		'RetType'=>$RetType,
		'GateId'=>$GateId,
		'ChkValue'=>$ChkValue));
		
		$result = array(
			'sn'=>$OrdId,
			'cope'=>$this->delFee($OrdAmt),
		);
		if( strpos($pageContents,'RespCode=000000',0)!==false){
	       if($RespCode == "000000"){
			//交易成功
			//根据订单号 进行相应业务操作
			//在些插入代码
			echo "RECV_ORD_AMT: [".$OrdAmt."]<BR>" ;
			echo "RECV_GATE_ID: [".$GateId."]<BR>" ;
			echo "RECV_USR_ID: [".$UsrId."]<BR>" ;
			echo "RECV_ORD_ID: [".$OrdId."]" ;
		   }else{
			   //交易失败
			   //根据订单号 进行相应业务操作
			   //在些插入代码
			   echo "支付失败";
			   $result = false;
		   } 
		}else{
		  //验签失败
		  echo "验签失败[".$pageContents."]";
		  $result = false;
		}
		echo "RECV_ORD_ID_".$OrdId;
		print_r("RECV_ORD_ID_".$OrdId);
		return $result;
	}
	
	public function _return_url(){
		    $CmdId 		= trim($_POST['CmdId']);
			$RespCode 		= trim($_POST['RespCode']);
			$UsrId 		= trim($_POST['UsrId']);
			$TrxId 	= trim($_POST['TrxId']);
			$OrdAmt 	= trim($_POST['OrdAmt']);
			$OrdId 	= trim($_POST['OrdId']);
			$MerPriv 	= trim($_POST['MerPriv']);
			$RetType 		= trim($_POST['RetType']);
			$GateId 	= trim($_POST['GateId']);
			$ChkValue 	= trim($_POST['ChkValue']);
			
			require_once("payment/chinapnr/HttpClient.class.php");
			$pageContents=httpClient::quickPost("{$this->transRespChk}?",array(
			'Version'=>"10",
            'CmdId'=>$CmdId,
            'RespCode'=>$RespCode,
            'UsrId'=>$UsrId,
            'TrxId'=>$TrxId,
            'OrdAmt'=>$OrdAmt,
            'OrdId'=>$OrdId,
            'MerPriv'=>$MerPriv,
            'RetType'=>$RetType,
            'GateId'=>$GateId,
            'ChkValue'=>$ChkValue));
			$result = array(
				'sn'=>$OrdId,
				'cope'=>$this->delFee($OrdAmt),
			);
			if( strpos($pageContents,'RespCode=000000',0)!==false){
		      if($RespCode == "000000"){
				return true;
		      }else{

			    return false;
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
		$para['UsrId'] = $data['sn'];		//请与贵网站订单系统中的唯一订单号匹配
		$para['OrdAmt']    = $this->addFee($data['cope']);	//订单总金额，显示在支付宝收银台里的“应付总额”里
		$para['Version']     = 10;
		$para['CmdId']       = "buy";
		$para['UsrMp']       = $this->partner;
		$para['MerPriv']     = '';
		$para['GateId'] 	 = getBlank($paybank)?getBlank($paybank):'';
		$para['Key']         = $this->key;
		//设置下小数点
		$para['OrdAmt'] = toPrice($para['OrdAmt'],2);
		$para['signMsg'] = $Version.$CmdId.$UsrId.$OrdId.$OrdAmt.$GateId.$UsrMp.$MerPriv.$Key;
		$para['ChkValue'] =   strtoupper(md5($signMsg));

		$sHtmlText = $this->buildForm($para);
		return $sHtmlText;
	}
	
	function buildForm($para) {		
		$sHtml = "<form id='paysubmit' action='".$this->buyTrans."' method='POST' target='_blank'>";
		while (list ($key, $val) = each ($para)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml = $sHtml."</form>";
		return $sHtml;
	}
	
	public function addFee($amount){
		//比例收取
		if($this->feetype){
			$amount = $amount + $amount * $this->fee;
		}else{
			//定额
			$amount = $amount + $this->fee;
		}
		return number_format($amount,2,'.','');
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
			'SPDB-B2B'=>'SPDBB2B', //上海浦东发展银行（B2B）*/
			'BOC'=>'45', //中国银行
			'ICBC'=>'25', //中国工商银行
			'CMB'=>'28', //招商银行
			'CCB'=>'27', //中国建设银行
			'ABC'=>'29', //中国农业银行
			'SPDB'=>'16', //上海浦东发展银行
			'CIB'=>'09', //兴业银行
			'GDB'=>'19', //广东发展银行
			'SDB'=>'14', //深圳发展银行
			'CMBC'=>'12', //中国民生银行
			'BCM'=>'21',//交通银行
			'CITIC'=>'33', //中信银行
			'HZB'=>'51', //杭州银行
			'CEB'=>'36', //中国光大银行
			//'SHB'=>'SHBANK', //上海银行
			'NBB'=>'52', //宁波银行
			'PAB'=>'50', //平安银行
			'BJNB'=>'40', //北京农村商业银行
			'FDB'=>'FDB', //富滇银行
			'PSBC'=>'46',// 中国邮政储蓄银行
			'BJB'=>'15', //北京银行
			'HXB'=>'13',//华夏银行
			'HXBEA'=>'48',//东亚银行
			'NJCB'=>'49',//南京银行
			'CZBANK'=>'53',//浙商银行
			/*'VISA'=>'abc1003', //visa
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