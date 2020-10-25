<?php
namespace Common\Api;

abstract class SmsApi{
	protected $username='';
	protected $password='';

	public $error='';
	private $type = '';

	/**
	 * 构造方法，配置应用信息
	 * @param array $token 
	 */
	public function __construct($token = null){

		$class = get_class($this);
		$this->type = strtoupper(substr($class, 0, strlen($class)-3));

		$username = C("SMS_USERNAME");
		$password = C("SMS_PASSWORD");
		if(empty($username) || empty($password)){
			throw new \Exception('请配置您'.$this->type.'短信平台的账号和密码');
		} else {
			$this->username  = $username;
			$this->password  = $password;
		}
	}

    public static function getInstance($type, $token = null) {
    	$name = ucfirst(strtolower($type)) . 'Sms';
    	require_once "Sms/{$name}.class.php";
    	if (class_exists($name)) {
    		return new $name($token);
    	} else {
    		throw new \Exception($name.'类与文件名不符');
    	}
    }

	/**
	 * 发送HTTP请求方法，目前只支持CURL发送请求
	 * @param  string $url    请求URL
	 * @param  array  $params 请求参数
	 * @param  string $method 请求方法GET/POST
	 * @return array  $data   响应数据
	 */
	protected function http($url, $params, $method = 'GET', $header = array(), $multi = false){
		$opts = array(
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HTTPHEADER     => $header
		);

		/* 根据请求类型设置特定参数 */
		switch(strtoupper($method)){
			case 'GET':
				$opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
				break;
			case 'POST':
				//判断是否传输文件
				$params = $multi ? $params : http_build_query($params);
				$opts[CURLOPT_URL] = $url;
				$opts[CURLOPT_POST] = 1;
				$opts[CURLOPT_POSTFIELDS] = $params;
				break;
			default:
				throw new Exception('不支持的请求方式！');
		}
		
		/* 初始化并执行curl请求 */
		$ch = curl_init();
		curl_setopt_array($ch, $opts);
		$data  = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		if($error) throw new Exception('请求发生错误：' . $error);
		return  $data;
	}
	
	/**
	 * 抽象方法,发送短信
	 * 组装接口调用参数 并调用接口
	 */
	abstract protected function send();

}