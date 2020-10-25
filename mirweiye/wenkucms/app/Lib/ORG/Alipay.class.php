<?php 
class Alipay{
		/* *
	 * 配置文件
	 * 版本：3.2
	 * 日期：2011-03-25
	 * 说明：
	 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
	 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
		
	 * 提示：如何获取安全校验码和合作身份者id
	 * 1.用您的签约支付宝账号登录支付宝网站(www.alipay.com)
	 * 2.点击“商家服务”(https://b.alipay.com/order/myorder.htm)
	 * 3.点击“查询合作者身份(pid)”、“查询安全校验码(key)”
		
	 * 安全校验码查看时，输入支付密码后，页面呈灰色的现象，怎么办？
	 * 解决方法：
	 * 1、检查浏览器配置，不让浏览器做弹框屏蔽设置
	 * 2、更换浏览器或电脑，重新登录查询。
	 */
	
	private $aliapy_config		= array();
	

	
	public function __construct($data){
		$this->setAliapy_config($data);
		
	}

	public function setAliapy_config($aliapy_config){
		//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
		//合作身份者id，以2088开头的16位纯数字
		$this->aliapy_config['partner']      = $aliapy_config['account'];
		
		//安全检验码，以数字和字母组成的32位字符
		$this->aliapy_config['key']          = $aliapy_config['key'];
		
		//签约支付宝账号或卖家支付宝帐户
		$this->aliapy_config['seller_email'] = $aliapy_config['merchant'];
		
		//页面跳转同步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		//return_url的域名不能写成http://localhost/create_direct_pay_by_user_php_utf8/return_url.php ，否则会导致return_url执行无效
		$this->aliapy_config['return_url']   = HOST.U('payment/alipay_return_url');
		
		//服务器异步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		$this->aliapy_config['notify_url']   = HOST.U('payment/alipay_notify_url');
		
		//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
		
		
		//签名方式 不需修改
		$this->aliapy_config['sign_type']    = 'MD5';
		
		//字符编码格式 目前支持 gbk 或 utf-8
		$this->aliapy_config['input_charset']= 'utf-8';
		
		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$this->aliapy_config['transport']    = 'http';
		return $this->aliapy_config;
	}
	
	public function _notify_url(){
		require_once("payment/alipay/alipay_notify.class.php");
			//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($this->aliapy_config);
		$verify_result = $alipayNotify->verifyNotify();
		
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
		    $out_trade_no	= $_POST['out_trade_no'];	    //获取订单号
		    $trade_no		= $_POST['trade_no'];	    	//获取支付宝交易号
		    $total_fee		= $_POST['total_fee'];			//获取总价格
			
		    $result = array(
				'sn'=>$out_trade_no,
				'cash'=>$total_fee,
			);
		    if($_POST['trade_status'] == 'TRADE_FINISHED' ||$_POST['trade_status'] == 'TRADE_SUCCESS') {    //交易成功结束
				//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序
		        
				echo "success";		//请不要修改或删除
				return $result;
		        //调试用，写文本函数记录程序运行情况是否正常
		        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		    }else {
		        echo "success";		//其他状态判断。普通即时到帐中，其他状态不用判断，直接打印success。
				return true;
		        //调试用，写文本函数记录程序运行情况是否正常
		        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		    }
			
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}else {
		    //验证失败
		    echo "fail";
			return false;
		    //调试用，写文本函数记录程序运行情况是否正常
		    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}
	
	public function _return_url(){
		require_once("payment/alipay/alipay_notify.class.php");		
			//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($this->aliapy_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
		    $out_trade_no	= $_GET['out_trade_no'];	//获取订单号
		    $trade_no		= $_GET['trade_no'];		//获取支付宝交易号
		    $total_fee		= $_GET['total_fee'];		//获取总价格
		    
			$result = array(
				'sn'=>$out_trade_no,
				'cash'=>$total_fee,
			);
		    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序
				return $result;
		    }else {
		      //echo "trade_status=".$_GET['trade_status'];
		        return false;
		    }
				
		}else {
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
	public function _payto($data,$payBlank){
		require_once("payment/alipay/alipay_service.class.php");
		/**************************请求参数**************************/
		$payBlank = $this->getBlank($payBlank);
		//必填参数//
		
		//请与贵网站订单系统中的唯一订单号匹配
		$out_trade_no = $data['sn'];
		//订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
		$subject      = $data['sn'];
		//订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
		$body         = '';
		//订单总金额，显示在支付宝收银台里的“应付总额”里
		$total_fee    =$data['cash'];
		
		//扩展功能参数——默认支付方式//
		
		//默认支付方式，取值见“即时到帐接口”技术文档中的请求参数列表
		$paymethod    = $payBlank?'bankPay':'';
		//默认网银代号，代号列表见“即时到帐接口”技术文档“附录”→“银行列表”
		$defaultbank  = $payBlank?$payBlank:'';
		
		
		//扩展功能参数——防钓鱼//
		
		//防钓鱼时间戳
		$anti_phishing_key  = '';
		//获取客户端的IP地址，建议：编写获取客户端IP地址的程序
		$exter_invoke_ip = '';
		//注意：
		//1.请慎重选择是否开启防钓鱼功能
		//2.exter_invoke_ip、anti_phishing_key一旦被使用过，那么它们就会成为必填参数
		//3.开启防钓鱼功能后，服务器、本机电脑必须支持SSL，请配置好该环境。
		//示例：
		//$exter_invoke_ip = '202.1.1.1';
		//$ali_service_timestamp = new AlipayService($aliapy_config);
		//$anti_phishing_key = $ali_service_timestamp->query_timestamp();//获取防钓鱼时间戳函数
		
		
		//扩展功能参数——其他//
		
		//商品展示地址，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		$show_url			= 	'';
		//自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
		$extra_common_param = '';
		
		//扩展功能参数——分润(若要使用，请按照注释要求的格式赋值)
		$royalty_type		= "";			//提成类型，该值为固定值：10，不需要修改
		$royalty_parameters	= "";
		//注意：
		//提成信息集，与需要结合商户网站自身情况动态获取每笔交易的各分润收款账号、各分润金额、各分润说明。最多只能设置10条
		//各分润金额的总和须小于等于total_fee
		//提成信息集格式为：收款方Email_1^金额1^备注1|收款方Email_2^金额2^备注2
		//示例：
		//royalty_type 		= "10"
		//royalty_parameters= "111@126.com^0.01^分润备注一|222@126.com^0.01^分润备注二"
		
		/************************************************************/
		
		//构造要请求的参数数组
		$parameter = array(
				"service"			=> "create_direct_pay_by_user",
				"payment_type"		=> "1",
				
				"partner"			=> trim($this->aliapy_config['partner']),
				"_input_charset"	=> trim(strtolower($this->aliapy_config['input_charset'])),
		        "seller_email"		=> trim($this->aliapy_config['seller_email']),
		        "return_url"		=> trim($this->aliapy_config['return_url']),
		        "notify_url"		=> trim($this->aliapy_config['notify_url']),
				
				"out_trade_no"		=> $out_trade_no,
				"subject"			=> $subject,
				"body"				=> $body,
				"total_fee"			=> $total_fee,
				
				"paymethod"			=> $paymethod,
				"defaultbank"		=> $defaultbank,
				
				"anti_phishing_key"	=> $anti_phishing_key,
				"exter_invoke_ip"	=> $exter_invoke_ip,
				
				"show_url"			=> $show_url,
				"extra_common_param"=> $extra_common_param,
				
				"royalty_type"		=> $royalty_type,
				"royalty_parameters"=> $royalty_parameters
		);
		
		//构造即时到帐接口
		$alipayService = new AlipayService($this->aliapy_config);
		$html_text = $alipayService->create_direct_pay_by_user($parameter);
		
		
		return  $html_text;

	}
	
	
	
	public function getBlank($payBlank){
		$bank = array(
			'ICBC-B2B'=>'ICBCBTB',//中国工商银行（B2B）
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
			'WZB-DEBIT'=>'WZCBB2C-DEBIT',//温州银行
		);
		return $bank['payBlank'];
	}
	
	
}
?>