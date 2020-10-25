<?php

namespace App\WechatHandler;

class WxMsg
{
    //接收消息部分
    const RECMSG_TYPE_TEXT        = 100; //文本消息
    const RECMSG_TYPE_IMAGE       = 101; //图片消息
    const RECMSG_TYPE_VOICE       = 102; //语音消息
    const RECMSG_TYPE_VIDEO       = 103; //视频消息
    const RECMSG_TYPE_SHORTVIDEO  = 104; //小视频消息
    const RECMSG_TYPE_LOCATION    = 105; //地理位置消息
    const RECMSG_TYPE_LINK        = 106; //链接消息
    const RECMSG_EVENT_SUBSCRIBE  = 200; //关注、取关注类事件
    const RECMSG_EVENT_SCAN       = 201; //扫描带参数二维码事件
    const RECMSG_EVENT_LOCATION   = 202; //上报地理位置事件
    const RECMSG_EVENT_MENU       = 203; //自定义菜单事件
    const RECMSG_EVENT_TEMPLATE   = 204; //模板消息事件
    const RECMSG_EVENT_KEFU       = 205; //客服消息推送事件
    const RECMSG_EVENT_MASS       = 206; //群发消息推送
    const RECMSG_EVENT_CARD       = 207; //卡券事件推送
    const RECMSG_EVENT_WIFI       = 208; //wifi事件推送
    const RECMSG_EVENT_POI        = 209; //微信门店事件推送
    const RECMSG_EVENT_SHAKEAROUND= 210; //摇一摇事件推送
    const RECMSG_EVENT_USERSCAN   = 211; //扫一扫事件推送

    //响应消息部分
    const SENDMSG_TYPE_TEXT     = 301; //文本消息
    const SENDMSG_TYPE_IMAGE    = 302; //图片消息
    const SENDMSG_TYPE_VOICE    = 303; //语音消息
    const SENDMSG_TYPE_VIDEO    = 304; //语音消息
    const SENDMSG_TYPE_LINK     = 305; //链接消息（暂不支持）
    const SENDMSG_TYPE_LOCATION = 306; //坐标消息（暂不支持）
    const SENDMSG_TYPE_MUSIC    = 307; //音乐消息
    const SENDMSG_TYPE_NEWS     = 308; //图文消息

    const SENDMSG_TYPE_ARTICLE  = 309; //文章消息
    const SENDMSG_TYPE_MATERIAL = 310; //素材消息

    /**
     * 接收消息.
     *
     * @var \App\WechatHandler\WxRecMessage
     */
    protected $recMessage;
    /**
     * 发送消息.
     *
     * @var \App\WechatHandler\WxSendMessage
     */
    protected $sendMessage;
    /**
     * 接收消息类别.
     *
     * @var int
     */
    protected $recMessageType = 0;
    /**
     * 发送消息类别.
     *
     * @var int
     */
    protected $sendMessageType = 0;

    /**
     * 设置接受消息类别.
     *
     * @param $message
     */
    public function setRecMessageType($message)
    {
        //判断消息是否是对象
        if (!is_object($message)) {
            return false;
        }
        $this->recMessage = $message;
        $msgType          = strtolower($this->recMessage->MsgType);
        //过滤空格
        $this->recMessage->MsgType = trim($this->recMessage->MsgType);
        $this->recMessage->Event = trim($this->recMessage->Event);

        $event            = strtolower($this->recMessage->Event);
        //文本消息推送
        if ($msgType == 'text') {
            $this->recMessageType = self::RECMSG_TYPE_TEXT;

            return true;
        }
        //图片消息推送
        if ($msgType == 'image') {
            $this->recMessageType = self::RECMSG_TYPE_IMAGE;

            return true;
        }
        //语音消息推送
        if ($msgType == 'voice') {
            $this->recMessageType = self::RECMSG_TYPE_VOICE;

            return true;
        }
        //视频消息推送
        if ($msgType == 'video') {
            $this->recMessageType = self::RECMSG_TYPE_VIDEO;

            return true;
        }
        //小视频消息推送
        if ($msgType == 'shortvideo') {
            $this->recMessageType = self::RECMSG_TYPE_SHORTVIDEO;

            return true;
        }
        //地理位置消息
        if ($msgType == 'location') {
            $this->recMessageType = self::RECMSG_TYPE_LOCATION;

            return true;
        }
        //链接消息
        if ($msgType == 'link') {
            $this->recMessageType = self::RECMSG_TYPE_LINK;

            return true;
        }
        //关注事件推送
        if ($msgType == 'event' && in_array($event, ['subscribe', 'unsubscribe']) && !isset($this->recMessage->EventKey)) {
            $this->recMessageType = self::RECMSG_EVENT_SUBSCRIBE;

            return true;
        }
        //扫码事件
        if ($msgType == 'event' && in_array($event, ['subscribe']) && isset($this->recMessage->EventKey) && strpos($this->recMessage->EventKey, 'qrscene_') !== false) {
            $this->recMessageType = self::RECMSG_EVENT_SCAN;

            return true;
        }
        if ($msgType == 'event' && in_array($event, ['scan']) && isset($this->recMessage->EventKey))
        {
            $this->recMessageType = self::RECMSG_EVENT_SCAN;

            return true;
        }
        //上报地理位置事件
        if ($msgType == 'event' && in_array($event, ['location'])) {
            $this->recMessageType = self::RECMSG_EVENT_LOCATION;

            return true;
        }
        //自定义菜单事件
        if ($msgType == 'event' && in_array($event, ['click', 'view','scancode_push','scancode_waitmsg','pic_sysphoto','pic_photo_or_album','pic_weixin','location_select'])) {
            $this->recMessageType = self::RECMSG_EVENT_MENU;

            return true;
        }
        //模板消息事件推送
        if ($msgType == 'event' && in_array($event, ['templatesendjobfinish'])) {
            $this->recMessageType = self::RECMSG_EVENT_TEMPLATE;

            return true;
        }
        //客服消息事件推送
        if ($msgType == 'event' && in_array($event, ['kf_create_session', 'kf_close_session', 'kf_switch_session'])) {
            $this->recMessageType = self::RECMSG_EVENT_KEFU;

            return true;
        }
        //群发消息事件推送
        if ($msgType == 'event' && in_array($event, ['masssendjobfinish'])) {
            $this->recMessageType = self::RECMSG_EVENT_MASS;
        }
        //卡券事件推送
        $cardEventList = ['card_pass_check', 'card_not_pass_check', 'user_get_card', 'user_gifting_card', 'user_del_card', 'user_consume_card', 'user_pay_from_pay_cell', 'user_view_card', 'update_member_card', 'card_sku_remind', 'card_pay_order', 'submit_membercard_user_info'];
        if ($msgType == 'event' && in_array($event, $cardEventList)) {
            $this->recMessageType = self::RECMSG_EVENT_CARD;
        }
        //wifi事件推送
        if ($msgType == 'event' && in_array($event, ['wificonnected'])) {
            $this->recMessageType = self::RECMSG_EVENT_WIFI;
        }
        //微信门店事件推送
        if ($msgType == 'event' && in_array($event, ['poi_check_notify'])) {
            $this->recMessageType = self::RECMSG_EVENT_POI;
        }
        //微信摇一摇事件推送
        if ($msgType == 'event' && in_array($event, ['shakearoundusershake', 'shakearoundlotterybind'])) {
            $this->recMessageType = self::RECMSG_EVENT_SHAKEAROUND;
        }
        //微信扫一扫事件推送
        if ($msgType == 'event' && in_array($event, ['user_scan_product', 'user_scan_product_enter_session', 'user_scan_product_async', 'user_scan_product_verify_action'])) {
            $this->recMessageType = self::RECMSG_EVENT_USERSCAN;
        }
        if ($msgType == 'event' && $event == 'subscribe' && in_array(strtolower($this->recMessage->EventKey), ['scene', 'keystandard', 'keystr', 'extinfo'])) {
            $this->recMessageType = self::RECMSG_EVENT_USERSCAN;
        }
    }

    /**
     * 获取接收消息类别.
     *
     * @return int
     */
    public function getRecMessageType()
    {
        return $this->recMessageType;
    }

    /**
     * 设置发送消息类别.
     *
     * @param array $xmlMessage
     *
     * @return bool|\SimpleXMLElement
     */
    public function setSendMessageType($xmlMessage = [])
    {
        //判断是不是xml格式
        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $xmlMessage, true)) {
            xml_parser_free($xml_parser);

            return false;
        }
        $message = simplexml_load_string($xmlMessage, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        if (!$message) {
            return false;
        }
        $this->sendMessage = $message;

        $msgType = strtolower($this->sendMessage->MsgType);
        $event   = strtolower($this->sendMessage->Event);
        //文本消息
        if ($msgType == 'text') {
            $this->sendMessageType = self::SENDMSG_TYPE_TEXT;

            return true;
        }
        //图片消息
        if ($msgType == 'image') {
            $this->sendMessageType = self::SENDMSG_TYPE_IMAGE;

            return true;
        }
        //语音消息
        if ($msgType == 'voice') {
            $this->sendMessageType = self::SENDMSG_TYPE_VOICE;

            return true;
        }
        //视频消息
        if ($msgType == 'video') {
            $this->sendMessageType = self::SENDMSG_TYPE_VIDEO;

            return true;
        }
        //音乐消息
        if ($msgType == 'music') {
            $this->sendMessageType == self::SENDMSG_TYPE_MUSIC;

            return true;
        }
        //图文消息
        if ($msgType == 'news') {
            $this->sendMessageType = self::SENDMSG_TYPE_NEWS;

            return true;
        }
    }

    /**
     * 获取发送消息类别.
     *
     * @return int
     */
    public function getSendMessageType()
    {
        return $this->sendMessageType;
    }

    /**
     * 通过素材数据转换为可以自动回复的响应格式.
     *
     * @param $materialData
     *
     * @return array|\EasyWeChat\Message\Image|\EasyWeChat\Message\Music|\EasyWeChat\Message\Text|\EasyWeChat\Message\Video|\EasyWeChat\Message\Voice
     */
    public function formatMessage($materialData)
    {
        if (!$materialData) {
            return false;
        }
        switch ($materialData['type']) {
            case 'text'://文本
                $materialData['content'] = \Swoole::$php->strip->unsetStrip($materialData['content']);
                return new \EasyWeChat\Message\Text(['content'=>$materialData['content']]);
                break;
            case 'video'://视频
                $mediaId = (new \App\Service\WxMaterial())->getMediaIdByMateriaId($materialData['articles']['material_id']);
                $materialData['articles']['title'] = \Swoole::$php->strip->unsetStrip($materialData['articles']['title']);
                $materialData['articles']['description'] = \Swoole::$php->strip->unsetStrip($materialData['articles']['description']);
                return new \EasyWeChat\Message\Video([
                    'title'       => $materialData['articles']['title'],
                    'description' => $materialData['articles']['description'],
                    'media_id'    => $mediaId,
                ]);
                break;
            case 'image'://图片
                $mediaId = (new \App\Service\WxMaterial())->getMediaIdByMateriaId($materialData['articles']['material_id']);

                return new \EasyWeChat\Message\Image(['media_id'=>$mediaId]);
                break;
            case 'voice'://声音
                $mediaId = (new \App\Service\WxMaterial())->getMediaIdByMateriaId($materialData['articles']['material_id']);

                return new \EasyWeChat\Message\Voice(['media_id'=>$mediaId]);
                break;
            case 'music'://音乐
                $mediaId = (new \App\Service\WxMaterial())->getMediaIdByMediaUrl('thumb', $materialData['thumb_url']);
                $materialData['title'] = \Swoole::$php->strip->unsetStrip($materialData['title']);
                $materialData['description'] = \Swoole::$php->strip->unsetStrip($materialData['description']);
                return new \EasyWeChat\Message\Music([
                    'title'          => $materialData['title'],
                    'description'    => $materialData['description'],
                    'url'            => $materialData['music_url'],
                    'hq_url'         => $materialData['hq_music_url'],
                    'thumb_media_id' => $mediaId,
                ]);
                break;
            case 'news'://多图文
                $news = [];
                foreach ($materialData['articles'] as $v) {
                    $v['title'] = \Swoole::$php->strip->unsetStrip($v['title']);
                    $v['description'] = \Swoole::$php->strip->unsetStrip($v['description']);
                    $news[] = new \EasyWeChat\Message\News([
                        'title'       => $v['title'],
                        'description' => $v['description'],
                        'url'         => $v['url'],
                        'image'       => $v['picurl'],
                    ]);
                }

                return $news;
                break;
            case 'transfer'://客服消息
                $transfer = new \EasyWeChat\Message\Transfer();
                if (isset($materialData['account']) && $materialData['account']){
                    $transfer->account($materialData['account']);
                }

                return $transfer;
                break;
        }
    }
}
