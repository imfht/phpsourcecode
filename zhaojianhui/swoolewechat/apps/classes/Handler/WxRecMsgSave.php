<?php

namespace App\Handler;

use App\WechatHandler\WxMsg;
use Swoole;

/**
 * 微信接收消息保存
 * @package App\Handler
 */
class WxRecMsgSave extends WxMsg implements Swoole\IFace\EventHandler
{
    public function trigger($type, $data)
    {
        if(!isset($data['message']) || !$data['message']){
            return false;
        }

        $this->setRecMessageType($data['message']);

        $saveData = [
            'MsgType'      => $this->recMessage->MsgType ?? '',
            'ToUserName'   => $this->recMessage->ToUserName ?? '',
            'FromUserName' => $this->recMessage->FromUserName ?? '',
            'CreateTime'   => $this->recMessage->CreateTime ?? 0,
        ];
        switch ($this->recMessageType){
            case self::RECMSG_TYPE_TEXT://文本消息
                $saveData['MsgId']   = $this->recMessage->MsgId ?? '';
                $saveData['Content'] = $this->recMessage->Content ?? '';
                $model           = model('WxmsgRecMsgText');
                break;
            case self::RECMSG_TYPE_IMAGE://图片消息
                $saveData['MsgId']   = $this->recMessage->MsgId ?? '';
                $saveData['MediaId'] = $this->recMessage->MediaId ?? '';
                $saveData['PicUrl']  = $this->recMessage->PicUrl ?? '';
                $model           = model('WxmsgRecMsgImage');
                break;
            case self::RECMSG_TYPE_VOICE://语音消息
                $saveData['MsgId']       = $this->recMessage->MsgId ?? '';
                $saveData['MediaId']     = $this->recMessage->MediaId ?? '';
                $saveData['Format']      = $this->recMessage->Format ?? '';
                $saveData['Recognition'] = $this->recMessage->Recognition ?? '';
                $model               = model('WxmsgRecMsgVoice');
                break;
            case self::RECMSG_TYPE_VIDEO://视频消息
                $saveData['MsgId']        = $this->recMessage->MsgId ?? '';
                $saveData['MediaId']      = $this->recMessage->MediaId ?? '';
                $saveData['ThumbMediaId'] = $this->recMessage->ThumbMediaId ?? '';
                $model                = model('WxmsgRecMsgVideo');
                break;
            case self::RECMSG_TYPE_SHORTVIDEO://小视频消息
                $saveData['MsgId']        = $this->recMessage->MsgId ?? '';
                $saveData['MediaId']      = $this->recMessage->MediaId ?? '';
                $saveData['ThumbMediaId'] = $this->recMessage->ThumbMediaId ?? '';
                $model                = model('WxmsgRecMsgShortvideo');
                break;
            case self::RECMSG_TYPE_LOCATION://地理位置消息
                $saveData['MsgId']       = $this->recMessage->MsgId ?? '';
                $saveData['Location_X']  = $this->recMessage->Location_X ?? 0;
                $saveData['Location_Y']  = $this->recMessage->Location_Y ?? 0;
                $saveData['Scale']       = $this->recMessage->Scale ?? 0;
                $saveData['Label']       = $this->recMessage->Label ?? '';
                $model               = model('WxmsgRecMsgLocation');
                break;
            case self::RECMSG_TYPE_LINK://链接消息
                $saveData['MsgId']       = $this->recMessage->MsgId ?? '';
                $saveData['Title']       = $this->recMessage->Title ?? '';
                $saveData['Description'] = $this->recMessage->Description ?? '';
                $saveData['Url']         = $this->recMessage->Description ?? '';
                $model               = model('WxmsgRecMsgLink');
                break;
            case self::RECMSG_EVENT_SUBSCRIBE://关注事件推送
                $saveData['Event'] = $this->recMessage->Event ?? '';
                $model         = model('WxmsgRecEventSubscribe');
                break;
            case self::RECMSG_EVENT_SCAN://扫码事件
                $saveData['Event']    = $this->recMessage->Event ?? '';
                $saveData['EventKey'] = $this->recMessage->EventKey ?? '';
                $saveData['Ticket']   = $this->recMessage->Ticket ?? '';
                $model            = model('WxmsgRecEventSubscribe');
                break;
            case self::RECMSG_EVENT_LOCATION://上报地理位置事件
                $saveData['Event']     = $this->recMessage->Event ?? '';
                $saveData['Latitude']  = $this->recMessage->Latitude ?? 0;
                $saveData['Longitude'] = $this->recMessage->Longitude ?? 0;
                $saveData['Precision'] = $this->recMessage->Precision ?? '';
                $model             = model('WxmsgRecEventLocation');
                break;
            case self::RECMSG_EVENT_MENU://自定义菜单事件
                $saveData['Event']    = $this->recMessage->Event ?? '';
                $saveData['EventKey'] = $this->recMessage->EventKey ?? '';
                $model            = model('WxmsgRecEventMenu');
                break;
            default:
                break;
        }
        if (isset($model) && is_callable([$model, 'put'])) {
            return $model->put($saveData);
        }
    }
}
