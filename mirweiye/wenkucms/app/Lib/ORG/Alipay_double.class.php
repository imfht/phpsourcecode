<?php 
class Alipay_double{

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
		$this->aliapy_config['return_url']   = HOST.U('payment/alipay_double_return_url');
		
		//服务器异步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		$this->aliapy_config['notify_url']   = HOST.U('payment/alipay_double_notify_url');
		
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
		require_once("payment/alipay_double/lib/alipay_notify.class.php");
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
            if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
                //该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款

                //判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                echo "success";		//请不要修改或删除
                return false;
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
                //该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货

                //判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                echo "success";		//请不要修改或删除
                return false;
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
                //该判断表示卖家已经发了货，但买家还没有做确认收货的操作

                //判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                echo "success";		//请不要修改或删除
                $this->shipping($trade_no);
                return $result;
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if($_POST['trade_status'] == 'TRADE_FINISHED') {
                //该判断表示买家已经确认收货，这笔交易完成

                //判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                echo "success";		//请不要修改或删除
                return $result;
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else {
                //其他状态判断
                echo "success";
                return false;
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult ("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
			
		}else {
		    //验证失败
		    echo "fail";
			return false;
		    //调试用，写文本函数记录程序运行情况是否正常
		    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}
	
	public function _return_url(){
		require_once("payment/alipay_double/lib/alipay_notify.class.php");
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
            $total_fee		= $_GET['price'];			//获取总价格
            $result = array(
                'sn'=>$out_trade_no,
                'cash'=>$total_fee,
            );
            if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
                //判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                //$this->shipping($trade_no);
                return $result;
            }
            else if($_GET['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                return $result;
            }
            /*else {
                echo "trade_status=".$_GET['trade_status'];
            }*/
            return false;

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数，比对sign和mysign的值是否相等，或者检查$responseTxt有没有返回true
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
		require_once("payment/alipay_double/lib/alipay_service.class.php");
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
		$total_fee    = $data['cash'];

		//构造要请求的参数数组
        $parameter = array(
            "service"			=> "trade_create_by_buyer",
            "payment_type"		=> "1",

            "partner"			=> trim($this->aliapy_config['partner']),
            "_input_charset"	=> trim(strtolower($this->aliapy_config['input_charset'])),
            "seller_email"		=> trim($this->aliapy_config['seller_email']),
            "return_url"		=> trim($this->aliapy_config['return_url']),
            "notify_url"		=> trim($this->aliapy_config['notify_url']),

            "out_trade_no"	=> $out_trade_no,
            "subject"		=> $subject,
            "body"			=> $body,
            "price"			=> $total_fee,
            "quantity"		=> '1',

            "logistics_fee"		=> '0.00',
            "logistics_type"	=> 'EXPRESS',
            "logistics_payment"	=> 'SELLER_PAY',

            "receive_name"		=> '',
            "receive_address"	=> '',
            "receive_zip"		=> '',
            "receive_phone"		=> '',
            "receive_mobile"	=> '',

            "show_url"		=> ''
        );
		//构造即时到帐接口
		$alipayService = new AlipayService($this->aliapy_config);
		$html_text = $alipayService->trade_create_by_buyer($parameter);
		return $html_text;
	}

    //发货
    public function shipping($sn){
        require_once("payment/alipay_double/shipping/alipay_service.class.php");
        //支付宝交易号。它是登陆支付宝网站在交易管理中查询得到，一般以8位日期开头的纯数字（如：20100419XXXXXXXXXX）
        $trade_no		= $sn;

        //物流公司名称
        $logistics_name	= C('sysconfig.site_name');

        //物流发货单号
        $invoice_no		= '';

        //物流发货时的运输类型，三个值可选：POST（平邮）、EXPRESS（快递）、EMS（EMS）
        $transport_type	= '';

        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service"			=> "send_goods_confirm_by_platform",
            "partner"			=> trim($this->aliapy_config['partner']),
            "_input_charset"	=> trim(strtolower($this->aliapy_config['input_charset'])),
            "trade_no"			=> $trade_no,
            "logistics_name"	=> $logistics_name,
            "invoice_no"		=> $invoice_no,
            "transport_type"	=> $transport_type
        );
        //构造确认发货接口
        $alipayService = new AlipayService($aliapy_config);
        $doc = $alipayService->send_goods_confirm_by_platform($parameter);

        //请在这里加上商户的业务逻辑程序代码

        //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
        //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
        //解析XML
       /* $response = '';
        if( ! empty($doc->getElementsByTagName( "response" )->item(0)->nodeValue) ) {
            $response= $doc->getElementsByTagName( "response" )->item(0)->nodeValue;
        }*/
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