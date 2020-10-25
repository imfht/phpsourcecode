<?php

namespace common\helpers;

/**
 * 阿里云支付辅助类
 *
 * @author ken <vb2005xu@qq.com>
 */
class Alipay
{

	/**
	 * 默认配置信息
	 * @var array 
	 */
	public $default_config = [
		//合作身份者id，以2088开头的16位纯数字
		'partner' => '',
		//安全检验码，以数字和字母组成的32位字符
		'key' => 'd6g3no5duhcsdddssstbl602xftj5p',
		//商家email
		'seller_email' => '',
		//商户的私钥（后缀是.pen）文件相对路径
		'private_key_path' => 'key/rsa_private_key.pem',
		//支付宝公钥（后缀是.pen）文件相对路径
		'ali_public_key_path' => 'key/alipay_public_key.pem',
		//签名方式 不需修改
		'sign_type' => 'RSA',
		//字符编码格式 目前支持 gbk 或 utf-8
		'input_charset' => 'utf-8',
		//请保证cacert.pem文件在当前文件夹目录中 getcwd().'\\cacert.pem
		'cacert' => '',
		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		'transport' => 'http',
	];

	/**
	 * 通知对象
	 * @var \AlipayNotify
	 */
	private $_notify = null;

	/**
	 * 构造函数
	 * @param type $config
	 */
	public function __construct($config = [])
	{
		$dir_root = \Yii::getAlias('@common');
		$this->default_config['partner'] = \Yii::$app->params['payment']['alipay']['partner'];
		$this->default_config['seller_email'] = \Yii::$app->params['payment']['alipay']['seller_email'];
		$this->default_config['cacert'] = getcwd() . '\\cacert.pem';
		$this->default_config['ali_public_key_path'] = $dir_root . '/lib/alipay/key/alipay_public_key.pem';
		if (!empty($config))
		{
			$this->default_config = array_merge($this->default_config, $config);
		}
		$this->_notify = new \AlipayNotify($this->default_config);
	}

	/**
	 * 验证数据
	 */
	function verify($data)
	{
		return $this->_notify->verifyNotify($data);
	}

	/**
	 * 针对return_url验证消息是否是支付宝发出的合法消息
	 * @return type
	 */
	function verifyReturn()
	{
		return $this->_notify->verifyReturn();
	}

	/**
	 * 支付订单
	 * @param string $subject 订单标题
	 * @param int $total_fee 价格
	 * @param string $out_trade_no 支付流水号
	 * @param string $return_url 页面跳转同步通知页面路径
	 * @return string
	 */
	function payOrder($subject, $total_fee, $out_trade_no, $return_url)
	{
		//支付类型
		$payment_type = "1";
		//必填，不能修改
		//服务器异步通知页面路径
//		$notify_url = "http://www.jiapai.cn/api/finances/alipay-callback";
		$notify_url = \Yii::$app->params['payment']['alipay']['notify_url'];
		//需http://格式的完整路径，不能加?id=123这类自定义参数
		//页面跳转同步通知页面路径
		//订单描述
		$body = 'test';
		//商品展示地址
		$show_url = 'test';
		//需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html
		//防钓鱼时间戳
		$anti_phishing_key = time();
		//若要使用请调用类文件submit中的query_timestamp函数
		//客户端的IP地址
		$exter_invoke_ip = "";
		//非局域网的外网IP地址，如：221.0.0.1
		$parameter = array(
			"service" => "create_direct_pay_by_user",
			"partner" => trim($this->default_config['partner']),
			"seller_email" => trim($this->default_config['seller_email']),
			"payment_type" => $payment_type,
			"notify_url" => $notify_url,
			"return_url" => $return_url,
			"out_trade_no" => $out_trade_no,
			"subject" => $subject,
			"total_fee" => $total_fee,
			"body" => $body,
			"show_url" => $show_url,
			"anti_phishing_key" => $anti_phishing_key,
			"exter_invoke_ip" => $exter_invoke_ip,
			"_input_charset" => trim(strtolower($this->default_config['input_charset']))
		);

		//建立请求
		$this->default_config['sign_type'] = 'MD5';
		$alipaySubmit = new \AlipaySubmit($this->default_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
		return $html_text;
	}

}

?>
