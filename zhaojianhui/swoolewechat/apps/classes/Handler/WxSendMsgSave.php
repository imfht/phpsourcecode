<?php
namespace App\Handler;

use Swoole;
use App\WechatHandler\WxMsg;
use EasyWeChat\Support\XML;

/**
 * 微信发送消息保存
 * @package App\Handler
 */
class WxSendMsgSave extends WxMsg implements Swoole\IFace\EventHandler
{
    public function trigger($type, $data)
    {
        if(!isset($data['message']) || !$data['message']){
            return false;
        }
        //判断是不是xml格式
        $xml_parser = xml_parser_create();
        if(!xml_parse($xml_parser, $data['message'],true)){
            xml_parser_free($xml_parser);
            return false;
        }
        $message = XML::parse($data['message']);
        if (!$message){
            return false;
        }
        $saveData = [
            'ToUserName' => $message['ToUserName'],
            'FromUserName' => $message['FromUserName'],
            'MsgType' => $message['MsgType'],
            'CreateTime' => $message['CreateTime'],
        ];
        unset($message['ToUserName'], $message['FromUserName'], $message['MsgType'], $message['CreateTime']);
        $saveData['ContentDetail'] = json_encode($message);
        return model('WxSendMsg')->put($saveData);
    }
}