<?php
/**
 *类名：alipay_service
 *功能：支付宝外部服务接口控制
 *详细：该页面是请求参数核心处理文件，不需要修改
 *版本：3.1
 *修改日期：2010-10-29
 '说明：
 '以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 '该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
*/

defined('IS_IN') or die('Include Error!');
require_once( RC_PATH_LIB . "alipay/class/alipay_function.php");
require_once( RC_PATH_LIB . "alipay/class/alipay_notify.php");

class alipay_service extends alipay_notify{

    var $gateway;	    //网关地址
    var $_key;		    //安全校验码
    var $mysign;	    //签名结果
    var $sign_type;	    //签名类型
    var $parameter;	    //需要签名的参数数组
    var $_input_charset;    //字符编码格式
    private $return_url;
    private $notify_url;
    private $show_url;

    /**
     * 构造函数（从配置文件及入口文件中初始化变量）
     * @param $aOrder array(
     *					'out_trade_no'	=> 'QC屋唯一订单号',
     *					'subject' 		=> "订单名称（商品名称）",
     *					'body'			=> "订单描述、订单详细、订单备注",
     *					'total_fee'		=> "订单总金额，显示在支付宝收银台里的“应付总额”里",
     *					'notify_url'	=> '异步处理程序',
     *					'return_url'	=> '同步跳转页面',
     *					'show_url'		=> '商品链接',
     *				   )
     */
    function alipay_service( $aOrder) {
    	$this->notify_url = $aOrder['notify_url'];
    	$this->return_url = $aOrder['return_url'];
    	$this->show_url   = $aOrder['show_url'];
		$aConfig = $this->loadAlipayConfig( @$aOrder['out_trade_no'], @$aOrder['subject'], @$aOrder['body'], @$aOrder['total_fee']);
		parent::alipay_notify($aConfig['partner'],$aConfig['key'],$aConfig['sign_type'],$aConfig['_input_charset'],$aConfig['transport']);
		$this->gateway = "https://www.alipay.com/cooperate/gateway.do?";
        $this->_key = $aConfig['key'];
        $this->sign_type = $aConfig['sign_type'];
        $this->parameter = para_filter( $aConfig['parameter']);

        //设定_input_charset的值,为空值的情况下默认为GBK
        if($aConfig['parameter']['_input_charset'] == '')
	    $this->parameter['_input_charset'] = 'utf-8';//GBK
        $this->_input_charset   = $this->parameter['_input_charset'];

        //获得签名结果
        $sort_array = arg_sort($this->parameter);    //得到从字母a到z排序后的签名参数数组
        $this->mysign = build_mysign($sort_array,$this->_key,$this->sign_type);
    }

    /**
     * 根据下单时的订单数据（返回需要参与签名的参数）
     * @param <type> $out_trade_no	QC屋唯一订单号
     * @param <type> $subject		订单名称（商品名称）
     * @param <type> $body		订单描述、订单详细、订单备注
     * @param <type> $total_fee		订单总金额，显示在支付宝收银台里的“应付总额”里
     * @return <type> array( 'parameter'=>'需要签名的参数数组', 'key'=>'安全校验码', 'sign_type'=>'签名类型');
     */
    function loadAlipayConfig( $out_trade_no='', $subject='', $body='', $total_fee=''){
		$partner = "2088002345270755";
		$key = "6y597k8okp13ocnp3xuy2q4gg03s8d6e";
		$seller_email = "diandengs@foxmail.com"; //签约支付宝账号或卖家支付宝帐户
		//$aUrl = $this->setGatwayUrl( $type);
		//$notify_url = $aUrl['notify_url'];	 //支付异步处理页面, 勿带参
		//$return_url = $aUrl['return_url'];	 //支付同步跳转页面,勿带参\
		$notify_url = $this->notify_url;
		$return_url = $this->return_url;
		$show_url = $this->show_url;		 //商品展示地址,勿带参
		$mainname = "QQC2C平台"; //收款公司名称
		$sign_type = "MD5"; //签名方式,勿改
		$_input_charset = "utf-8"; //字符编码格式,GBK 或 utf-8
		$transport = "http"; //服务器支持ssl则https，否则http
		//扩展功能参数——默认支付方式
		$pay_mode = 'directPay';
		if ($pay_mode != "directPay") {
		    $paymethod = "directPay";    //默认支付方式，四个值可选：bankPay(网银); cartoon(卡通); directPay(余额); CASH(网点支付)
		    $defaultbank = "";
		} else {
		    $paymethod = "bankPay";
		    $defaultbank = $pay_mode;     //默认网银代号，代号列表见http://club.alipay.com/read.php?tid=8681379
		}
	
		//扩展功能参数——防钓鱼
		//请慎重选择是否开启防钓鱼功能
		//exter_invoke_ip、anti_phishing_key一旦被使用过，那么它们就会成为必填参数
		//开启防钓鱼功能后，服务器、本机电脑必须支持远程XML解析，请配置好该环境。
		//若要使用防钓鱼功能，请打开class文件夹中alipay_function.php文件，找到该文件最下方的query_timestamp函数，根据注释对该函数进行修改
		//建议使用POST方式请求数据
		$anti_phishing_key = '';     //防钓鱼时间戳（如：query_timestamp($partner);//获取防钓鱼时间戳函数）
		$exter_invoke_ip = '';      //获取客户端的IP地址，建议：编写获取客户端IP地址的程序（如：'202.1.1.1'）
		//扩展功能参数——其他
		$extra_common_param = '';     //自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
		$buyer_email = '';      //默认买家支付宝账号
		//扩展功能参数——分润(若要使用，请按照注释要求的格式赋值)
		$royalty_type = "";      //提成类型，该值为固定值：10，不需要修改（如：10）
		$royalty_parameters = "";     //提成信息集（如："111@126.com^0.01^分润备注一|222@126.com^0.01^分润备注二"）
		//提成信息集，与需要结合商户网站自身情况动态获取每笔交易的各分润收款账号、各分润金额、各分润说明。最多只能设置10条
		//各分润金额的总和须小于等于total_fee
		//提成信息集格式为：收款方Email_1^金额1^备注1|收款方Email_2^金额2^备注2
		
		//构造要请求的参数数组，无需改动
		$parameter = array(
		    "service" => "create_direct_pay_by_user", //接口名称，不需要修改
		    "payment_type" => "1", //交易类型，不需要修改
		    //获取配置文件(alipay_config.php)中的值
		    "partner" => $partner,
		    "seller_email" => $seller_email,
		    "return_url" => $return_url,
		    "notify_url" => $notify_url,
		    "_input_charset" => $_input_charset,
		    "show_url" => $show_url,
		    //从订单数据中动态获取到的必填参数
		    "out_trade_no" => $out_trade_no,
		    "subject" => $subject,
		    "body" => $body,
		    "total_fee" => $total_fee,
		    //扩展功能参数——网银提前
		    "paymethod" => $paymethod,
		    "defaultbank" => $defaultbank,
		    //扩展功能参数——防钓鱼
		    "anti_phishing_key" => $anti_phishing_key,
		    "exter_invoke_ip" => $exter_invoke_ip,
		    //扩展功能参数——自定义参数
		    "buyer_email" => $buyer_email,
		    "extra_common_param" => $extra_common_param,
		    //扩展功能参数——分润
		    "royalty_type" => $royalty_type,
		    "royalty_parameters" => $royalty_parameters
		);
		return array('parameter' => $parameter, 'key' => $key, 'sign_type' => $sign_type, 'partner'=>$partner, '_input_charset'=>$_input_charset, 'transport'=>$transport,);
    }
    /**
     * 根据业务类型设置相应支付宝支付后的业务处理地址
     * @param <type> $type 
     */
    function setGatwayUrl( $type){
		$aUrl = array();
		if( $type==0){//QQ空间QCC的支付宝业务处理网关
		    $aUrl['notify_url'] = rc::$construct['buyqcc_notify_url']; //支付异步处理页面, 勿带参
		    $aUrl['return_url'] = rc::$construct['buyqcc_return_url']; //支付同步跳转页面,勿带参
		    $aUrl['show_url'] = "http://www.qqc2c.com"; //商品展示地址,勿带参
		}
		return $aUrl;
    }
    
    /**
     * 构造表单提交HTML
     * @return string 表单提交HTML文本
     */
    function build_form() {
		//GET方式传递（用post则更换form为post）
		$sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->gateway."_input_charset=".$this->parameter['_input_charset']."' method='get'>";
        while (list ($key, $val) = each ($this->parameter)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml .= "<input type='hidden' name='sign' value='".$this->mysign."'/>";
        $sHtml .= "<input type='hidden' name='sign_type' value='".$this->sign_type."'/>";
		//submit按钮控件请不要含有name属性
        //$sHtml .= "<input type='submit' value='支付宝确认付款'></form>";
		$sHtml .= "<script>document.forms['alipaysubmit'].submit();</script>";
        return $sHtml;
    }
    
    /**
     * auth:recho
     */
    function getPayUrl(){
    	$url = $this->gateway."_input_charset=".$this->parameter['_input_charset'];
    	while (list ($key, $val) = each ($this->parameter)) {
            $url .= "&$key=$val";
        }
        $url .= "&sign={$this->mysign}";
        $url .= "&sign_type={$this->sign_type}";
        return $url;
    }
}