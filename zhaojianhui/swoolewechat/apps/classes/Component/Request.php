<?php

namespace App\Component;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * 增强Request类.
 */
class Request extends SymfonyRequest
{
    /**
     * 增强Symfony的Request类，这里为了能支持swoole获取fopen('php://input')的代替方式.
     *
     * @param bool $asResource
     *
     * @return string
     */
    public function getContent($asResource = false)
    {
        //文字消息模拟
        //return "<xml><ToUserName><![CDATA[gh_570de6da66f2]]></ToUserName>\n<FromUserName><![CDATA[og26Wt5KKativAinwbfPLAqfhwzM]]></FromUserName>\n<CreateTime>1494426074</CreateTime>\n<MsgType><![CDATA[text]]></MsgType>\n<Content><![CDATA[客服]]></Content>\n<MsgId>6418511114588264346</MsgId>\n</xml>";
        //图片消息模拟
        //return "<xml><ToUserName><![CDATA[gh_570de6da66f2]]></ToUserName>\n<FromUserName><![CDATA[ogKdPt-LQTpRjBSRQYEZwNN2dGE4]]></FromUserName>\n<CreateTime>1494425996</CreateTime>\n<MsgType><![CDATA[image]]></MsgType>\n<PicUrl><![CDATA[http://mmbiz.qpic.cn/mmbiz_jpg/TkwNNc2rue0ZFhiaOCcuyuTZbDB0JoR9gGaTBgayr1kJV1EtaZ0gq7YPfs22EH8Oeu3Yo2lqZxe7bjnTcuZE9Tg/0]]></PicUrl>\n<MsgId>6418510779580815229</MsgId>\n<MediaId><![CDATA[C8lqeWDeNLd0L9l0UzfmlT5sTuJnZ25IoGN2YEaGKS0IQImV_0niSQgSJ_pisONX]]></MediaId>\n</xml>";
        //语音消息模拟
        //return "<xml><ToUserName><![CDATA[gh_570de6da66f2]]></ToUserName>\n<FromUserName><![CDATA[ogKdPt-LQTpRjBSRQYEZwNN2dGE4]]></FromUserName>\n<CreateTime>1494427217</CreateTime>\n<MsgType><![CDATA[voice]]></MsgType>\n<MediaId><![CDATA[q0bZNUEgwsPfbqxuBT33yuVx64vyns_sZ0Ql4gV5z9kHqBizXvTXBphAXp6SNb6_]]></MediaId>\n<Format><![CDATA[amr]]></Format>\n<MsgId>6418516023735884259</MsgId>\n<Recognition><![CDATA[]]></Recognition>\n</xml>";
        //视频消息模拟
        //return "<xml><ToUserName><![CDATA[gh_570de6da66f2]]></ToUserName>\n<FromUserName><![CDATA[ogKdPt-LQTpRjBSRQYEZwNN2dGE4]]></FromUserName>\n<CreateTime>1494426045</CreateTime>\n<MsgType><![CDATA[video]]></MsgType>\n<MediaId><![CDATA[wIvUHGkFLdjbXK9TTnto4BkqA3BmgpxzgUcnN0WgfpvNpV8_JAo7qRYc2wW4_M-j]]></MediaId>\n<ThumbMediaId><![CDATA[UNDVEiILx9bVZrDj1rZfEGtHwrD9E5R9KCFTS92MoS48QEJZKRah6mArx-Mq4qM9]]></ThumbMediaId>\n<MsgId>6418510990034212748</MsgId>\n</xml>";
        //位置消息模拟
        //return "<xml><ToUserName><![CDATA[gh_570de6da66f2]]></ToUserName>\n<FromUserName><![CDATA[ogKdPt-LQTpRjBSRQYEZwNN2dGE4]]></FromUserName>\n<CreateTime>1494427245</CreateTime>\n<MsgType><![CDATA[location]]></MsgType>\n<Location_X>23.163338</Location_X>\n<Location_Y>113.382883</Location_Y>\n<Scale>15</Scale>\n<Label><![CDATA[广州市天河区科韵北路福永商场(红花岗商场东北)]]></Label>\n<MsgId>6418516143994968574</MsgId>\n</xml>";
        //链接消息模拟
        //return "<xml><ToUserName><![CDATA[gh_570de6da66f2]]></ToUserName>\n<FromUserName><![CDATA[ogKdPt-LQTpRjBSRQYEZwNN2dGE4]]></FromUserName>\n<CreateTime>1494429650</CreateTime>\n<MsgType><![CDATA[link]]></MsgType>\n<Title><![CDATA[如何在不停机的情况下，完成百万级数据跨表迁移？]]></Title>\n<Description><![CDATA[Stripe与大家分享了他们在不停服的情况下如何做大规模数据在线迁移的经验，尤其是数据模型发生改变时。]]></Description>\n<Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MzA5Nzc4OTA1Mw==&mid=2659599212&idx=1&sn=9e077c749339923c33ca5abbf73ed22c&chksm=8be9967ebc9e1f689cc20cb15c7abde5bcf34ab310de4fa0d22ede183f25de0c718cf905801a&mpshare=1&scene=24&srcid=0425mAMcR71cvv5U3jArIXbW#rd]]></Url>\n<MsgId>6418526473391316037</MsgId>\n</xml>";
        //自定义菜单点击事件模拟
        //return "<xml><ToUserName><![CDATA[gh_570de6da66f2]]></ToUserName>\n<FromUserName><![CDATA[ogKdPt-LQTpRjBSRQYEZwNN2dGE4]]></FromUserName>\n<CreateTime>1494427415</CreateTime>\n<MsgType><![CDATA[event]]></MsgType>\n<Event><![CDATA[CLICK]]></Event>\n<EventKey><![CDATA[search]]></EventKey>\n</xml>";
        //关注事件模拟
        //return "<xml><ToUserName><![CDATA[gh_570de6da66f2]]></ToUserName>\n<FromUserName><![CDATA[ogKdPt-LQTpRjBSRQYEZwNN2dGE4]]></FromUserName>\n<CreateTime>1494430060</CreateTime>\n<MsgType><![CDATA[event]]></MsgType>\n<Event><![CDATA[subscribe]]></Event>\n<EventKey><![CDATA[]]></EventKey>\n</xml>";
        //取消关注事件模拟
        //return "<xml><ToUserName><![CDATA[gh_570de6da66f2]]></ToUserName>\n<FromUserName><![CDATA[ogKdPt-LQTpRjBSRQYEZwNN2dGE4]]></FromUserName>\n<CreateTime>1494430044</CreateTime>\n<MsgType><![CDATA[event]]></MsgType>\n<Event><![CDATA[unsubscribe]]></Event>\n<EventKey><![CDATA[]]></EventKey>\n</xml>";
        //点击菜单拉取消息时的事件推送
        //return '<xml><ToUserName><![CDATA[gh_570de6da66f2]]></ToUserName><FromUserName><![CDATA[og26Wt5KKativAinwbfPLAqfhwzM]]></FromUserName><CreateTime>1348831860</CreateTime><MsgType><![CDATA[event]]></MsgType><Event><![CDATA[CLICK]]></Event><EventKey><![CDATA[15]]></EventKey></xml>';
        //wifi连接事件
        //return '<xml><ToUserName><![CDATA[gh_570de6da66f2]]></ToUserName><FromUserName><![CDATA[ogKdPt-LQTpRjBSRQYEZwNN2dGE4]]></FromUserName><CreateTime>1348831860</CreateTime><MsgType><![CDATA[event]]></MsgType><Event><![CDATA[WifiConnected]]></Event><ConnectTime>1496585705</ConnectTime><ExpireTime>1496678400</ExpireTime><VendorId><![CDATA[3001224419]]></VendorId><ShopId><![CDATA[4983322]]></ShopId><DeviceNo><![CDATA[DeviceNo]]></DeviceNo></xml>';
        parent::getContent($asResource); // TODO: Change the autogenerated stub

        if (null == $this->content || false === $this->content) {
            $this->content = \Swoole::getInstance()->http->getRequestBody();
        }

        return $this->content;
    }
}
