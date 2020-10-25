<?php 
class Chinabank{
	//****************************************
	private  $v_mid = '';								    // 商户号，这里为测试商户号1001，替换为自己的商户号(老版商户号为4位或5位,新版为8位)即可
	private  $v_url = '';	// 请填写返回url,地址应为绝对路径,带有http协议
	private  $key   = '';								    // 如果您还没有设置MD5密钥请登陆我们为您提供商户后台，地址：https://merchant3.chinabank.com.cn/
														// 登陆后在上面的导航栏里可能找到“B2C”，在二级导航栏里有“MD5密钥设置” 
														// 建议您设置一个16位以上的密钥或更高，密钥最多64位，但设置16位已经足够了
	private  $url = 'https://Pay3.chinabank.com.cn/PayGate';
	//****************************************
	private $feetype = '';
	
	private $fee = '';
	public function __construct($data){
		$this->v_mid = $data['account'];
		$this->key = $data['key'];
		$this->v_url = HOST.U('/Payment/chinabank_return_url');
		$this->feetype = $data['feetype'];
		$this->fee = $data['fee'];
	}
	
	
	public function _return_url(){			
		$v_oid     =trim($_POST['v_oid']);       // 商户发送的v_oid定单编号   
		$v_pmode   =trim($_POST['v_pmode']);    // 支付方式（字符串）   
		$v_pstatus =trim($_POST['v_pstatus']);   //  支付状态 ：20（支付成功）；30（支付失败）
		$v_pstring =trim($_POST['v_pstring']);   // 支付结果信息 ： 支付完成（当v_pstatus=20时）；失败原因（当v_pstatus=30时,字符串）； 
		$v_amount  =trim($_POST['v_amount']);     // 订单实际支付金额
		$v_moneytype  =trim($_POST['v_moneytype']); //订单实际支付币种    
		$remark1   =trim($_POST['remark1']);      //备注字段1
		$remark2   =trim($_POST['remark2']);     //备注字段2
		$v_md5str  =trim($_POST['v_md5str' ]);   //拼凑后的MD5校验值 
		
		$md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$this->key));
		
		$result = array(
			'sn'=>$v_oid,
			'cope'=>$this->delFee($v_amount),
		);
		
		if ($v_md5str==$md5string){
			if($v_pstatus=="20")
			{
				//支付成功，可进行逻辑处理！
				return $result;//商户系统的逻辑处理（例如判断金额，判断支付状态，更新订单状态等等）......
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
		/*以下参数是需要通过下单时的订单数据传入进来获得*/
		//必填参数
		$v_oid = $data['sn'];		//请与贵网站订单系统中的唯一订单号匹配
		//$remark1      = $data['goodsname'];	//订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
		//$remark2      = $data['remark'];	//订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
		$v_amount    = $this->addFee($data['cope']);	//订单总金额，显示在支付宝收银台里的“应付总额”里
		
		$v_moneytype = "CNY";                                            //币种
		$text = $v_amount.$v_moneytype.$v_oid.$this->v_mid.$this->v_url.$this->key;        //md5加密拼凑串,注意顺序不能变
		$v_md5info = strtoupper(md5($text));                             //md5函数加密并转化成大写字母
		
		$redata = array(
			'v_mid'=>$this->v_mid,
			'v_oid'=>$v_oid,
			'v_amount'=>$v_amount,
			'v_moneytype'=>$v_moneytype,
			'v_url'=>$this->v_url,
			'v_md5info'=>$v_md5info,
		);

		$sHtml = $this->buildForm($redata);		
		return $sHtml;
		
	}
	
	function buildForm($para) {		
		$sHtml = "<form id='paysubmit' action='".$this->url."' method='POST' target='_blank'>";
		while (list ($key, $val) = each ($para)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml = $sHtml."</form>";
		return $sHtml;
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