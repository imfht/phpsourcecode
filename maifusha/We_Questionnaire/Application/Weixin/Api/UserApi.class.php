<?php
namespace Weixin\Api;
use Weixin\Api\BaseApi;

/**
 * 用户管理Api
 */
class UserApi extends BaseApi
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取当前关注用户openid列表
     * @return array  关注者的openid数组
     */
    public function getSubscriberIDs()
    {
        $interface = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$this->getAccessToken()}";
        $responseSeq = httpGet($interface);

        try{
            $result = $this->responseValidate($responseSeq);
        } catch( \Exception $e ){
            $errcode = $e->getCode();
            $errmsg = $e->getMessage();

            /* 再次上抛异常至TP设定的顶层异常处理器中, 输出异常处理模板 */
            throw new \Think\Exception("获取用户列表失败 <br /> 消息: $errmsg <br /> 错误码: $errcode", $errcode);
        }

        return $result['data']['openid'];
    }

    /**
     * 获取指定用户的个人信息
     * @param string $openid  指定用户的id
     * @return assoc-array  用户个人信息的关联数组
     */
    public function getUserInfo($openid)
    {
        $interface = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$this->getAccessToken()}&openid=$openid&lang=zh_CN";
        $responseSeq = httpGet($interface);

        try{
            $result = $this->responseValidate($responseSeq);
        } catch( \Exception $e ){
            $errcode = $e->getCode();
            $errmsg = $e->getMessage();

            /* 再次上抛异常至TP设定的顶层异常处理器中, 输出异常处理模板 */
            throw new \Think\Exception("获取用户 $openid 的个人信息失败 <br /> 消息: $errmsg <br /> 错误码: $errcode", $errcode);
        }

        return $result;
    }
}
