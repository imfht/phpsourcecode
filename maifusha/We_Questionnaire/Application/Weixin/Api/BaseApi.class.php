<?php
/* Weixin模块的Api层代码都运行在公众号连接的主动调用模式下 */
namespace Weixin\Api;

if( !defined('WEIXIN_MODULE_PATH') ){
	/* Api层非通过路由进入，而是被其他层调用，所以需要手动载入依赖文件 */
	define('WEIXIN_MODULE_PATH', dirname(dirname(__FILE__)));
	require_once WEIXIN_MODULE_PATH . '/Common/function.php'; //载入微信模块函数库文件
}


/**
 * Api公共基类
 */
class BaseApi
{
	protected $AppID;
	protected $AppSecret;
	protected $domain;	
	protected $Token;
	protected $EncodingAESKey;
	protected $cryptType;	

	public function __construct()
	{
		if( method_exists($this, '_initialize') ){
			$this->_initialize();
		}
	}

	protected function _initialize()
	{
        (new \Common\Controller\BaseController())->loadSettings(); //加载应用配置

		/* 读取微信公众号配置到当前Api对象 */
		$this->AppID = C('settings.weixin_AppID');
		$this->AppSecret = C('settings.weixin_AppSecret');
		$this->domain = C('settings.weixin_domain');	
		$this->Token = C('settings.weixin_Token');
		$this->EncodingAESKey = C('settings.weixin_EncodingAESKey');
		$this->cryptType = C('settings.weixin_cryptType');	

		/* 缓存初始化 */
		S(array(
			'type' => 'file',
			'expire' => 7000,	//缓存超时控制在2小时内，并提前200秒以及时刷新令牌
			'prefix' => 'questionnaire',
		));
	}

	/**
	 * 获取一个有效期内的令牌，过期的话重新请求
	 */
	public function getAccessToken()
	{
		/* 缓存了有效令牌 */
		if( $access_token = S('access_token') )
			return $access_token;

		/* 令牌缓存超时或者不存在，重新获取 */
		$interface = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->AppID}&secret={$this->AppSecret}";
		$responseSeq = httpGet($interface);

		try {
			$result = $this->responseValidate($responseSeq);
		} catch ( \Exception $e ) {
			$errcode = $e->getCode();
			$errmsg = $e->getMessage();

			/* 再次上抛异常至TP设定的顶层异常处理器中, 输出异常处理模板 */
			throw new \Think\Exception("微信令牌接口请求错误 <br /> 消息: $errmsg <br /> 错误码: $errcode", $errcode);
		}

		S('access_token', $result['access_token']); //缓存令牌
		return $result['access_token'];			
	}

	/**
	 * 微信接口响应数据的有效性验证
	 * @param string $responseSeq  公众号接口返回的json字符串
	 * @return 有效: json字符串解析出的数组数据    无效: 抛出异常
	 */
	protected function responseValidate($responseSeq)
	{
		$response = json_decode($responseSeq, true);

		/* 响应数据中错误码置位且不为0, 则抛出一个异常，异常携带了微信错误信息 */
		if( isset($response['errcode'] ) && $response['errcode'] ){
			throw new \Exception($response['errmsg'], $response['errcode']);
		}else{
			return $response;
		}
	}	

}
?>