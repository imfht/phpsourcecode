<?php 
/**
 * 抽象事件控制器
 */
namespace Weixin\Event;
use Think\Controlelr;

class BaseEvent extends Controlelr
{
	protected function _initialize()
	{
		//
	}

	/**
     * 被动回复用户消息
     */
    public function responseMsg()
    {
		$postStr = file_get_contents("php://input");

		if( !empty($postStr) ){
          	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";             
			if(!empty( $keyword ))
            {
          		$msgType = "text";
            	$contentStr = "Welcome to wechat world!";
            	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            	echo $resultStr;
            }else{
            	echo "Input something...";
            }
        }else{
        	echo "" AND exit;
        }
    }

}
?>