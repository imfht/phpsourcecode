<?php 
namespace Weixin\Api;
use Weixin\Api\BaseApi;

/**
 * 网页授权并获取用户信息相关Api
 */
class WebpageApi extends BaseApi
{
	protected function _initialize()
    {
        parent::_initialize();
        //
    }

	/**
	 * 向公众平台请求网页授权access_token
	 * @param string $code  临时授权码
	 * @return string  网页授权令牌
	 */
	public function	getWebpageAccessToken($code)
	{
		$interface = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->AppID&secret=$this->AppSecret&code=$code&grant_type=authorization_code";
		$responseSeq = httpGet($interface);

		try {
			$result = $this->responseValidate($responseSeq);
		} catch ( \Exception $e ) {
			$errcode = $e->getCode();
			$errmsg = $e->getMessage();

			/* 再次上抛异常至TP设定的顶层异常处理器中, 输出异常处理模板 */
			throw new \Think\Exception("微信网页授权令牌接口请求错误 <br /> 消息: $errmsg <br /> 错误码: $errcode", $errcode);
		}

		session('openid', $result['openid']); //会话记录当前答题者的openid

		return $result['access_token'];			
	}

	/**
	 * scope为snsapi_userinfo下拉取用户信息
	 * @param string $openid  公众号平台下的用户id
	 * @param string $access_token  网页授权令牌
	 * @return assoc-array  包含用户信息的关联数组
	 */
	public function pullUserinfo($openid, $access_token)
	{
		$interface = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
		$responseSeq = httpGet($interface);

		try {
			$result = $this->responseValidate($responseSeq);
		} catch ( \Exception $e ) {
			$errcode = $e->getCode();
			$errmsg = $e->getMessage();

			/* 再次上抛异常至TP设定的顶层异常处理器中, 输出异常处理模板 */
			throw new \Think\Exception("微信网页授权拉取用户信息失败 <br /> 消息: $errmsg <br /> 错误码: $errcode", $errcode);
		}

		return $result;
	}
    
}
?>