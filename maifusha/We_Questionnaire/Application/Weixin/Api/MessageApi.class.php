<?php 
namespace Weixin\Api;
use Weixin\Api\BaseApi;

/**
 * 向关注者发送消息Api
 * 由于一般群发接口有频率限制，所以这里所有的api方法通过没有频率限制的预览接口来绕过
 */
class MessageApi extends BaseApi
{
	protected function _initialize()
    {
        parent::_initialize();
        //
    }

    /**
     * 根据分组进行群发
     * @param string $group  分组id
     * @param string $msg  消息体
     */
    public function sendTOGroup($group, $msg)
    {
        //TODO: 后续完成向分组群发功能
    }
    
    /**
     * 根据OpenID列表群发
     * @param array $openids  用户id列表
     * @param string $msg  消息体
     */
    public function sendToIDS($openids, $msg)
    {
        foreach($openids as $openid){
            $this->sendToID($openid, $msg);
        }
    }

    /**
     * 向指定用户发送消息
     * @param string $openid  用户id
     * @param string $msg  消息体
     */
    public function sendToID($openid, $msg)
    {
        $interface = "https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token={$this->getAccessToken()}";
        $msg = sprintf($msg, $openid);
        $responseSeq = httpPost($interface, $msg);

        try {
            $result = $this->responseValidate($responseSeq);
        } catch ( \Exception $e ) {
            $errcode = $e->getCode();
            $errmsg = $e->getMessage();

            $this->error("向用户 $openid 发送消息失败 <br /> 返回消息: $errmsg <br /> 错误码: $errcode");
        }
    }

}
?>