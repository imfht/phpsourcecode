<?php

namespace App\WechatHandler;

/**
 * 微信发送消息提示类，该消息不做任何功能，仅仅为了IDE能自动提示.
 *
 * @property \App\WechatHandler\WxImage $Image
 * @property \App\WechatHandler\WxVoice $Voice
 * @property \App\WechatHandler\WxVideo $Video
 * @property \App\WechatHandler\WxMusic $Music
 * @property \App\WechatHandler\WxArticles $Articles
 */
class WxSendMessage
{
    //公共属性
    public $ToUserName;
    public $FromUserName;
    public $CreateTime;
    public $MsgType;
    //普通消息-文本
    public $Content;
    /**
     * 图片元素.
     *
     * @var
     */
    public $Image;
    /**
     * 语音元素.
     *
     * @var
     */
    public $Voice;
    /**
     * 视频元素.
     *
     * @var
     */
    public $Video;
    /**
     * 音乐元素.
     *
     * @var
     */
    public $Music;
    /**
     * 图文消息个数，限制为8条以内.
     *
     * @var
     */
    public $ArticleCount;
    /**
     * 多条图文消息信息，默认第一个item为大图,注意，如果图文数超过8，则将会无响应.
     *
     * @var
     */
    public $Articles;
}

/**
 * 图片消息-图片元素子类.
 */
class WxImage
{
    /**
     * 通过素材管理中的接口上传多媒体文件，得到的id。
     *
     * @var
     */
    public $MediaId;
}

/**
 * 语音消息-语音元素子类.
 */
class WxVoice
{
    /**
     * 通过素材管理中的接口上传多媒体文件，得到的id.
     *
     * @var
     */
    public $MediaId;
}

/**
 * 视频消息-视频元素子类.
 */
class WxVideo
{
    /**
     * 通过素材管理中的接口上传多媒体文件，得到的id.
     *
     * @var
     */
    public $MediaId;
    /**
     * 视频消息的标题.
     *
     * @var
     */
    public $Title;
    /**
     * 视频消息的描述.
     *
     * @var
     */
    public $Description;
}

/**
 * 音乐消息-音乐元素子类.
 */
class WxMusic
{
    /**
     * 音乐标题.
     *
     * @var
     */
    public $Title;
    /**
     * 音乐描述.
     *
     * @var
     */
    public $Description;
    /**
     * 音乐链接.
     *
     * @var
     */
    public $MusicUrl;
    /**
     * 高质量音乐链接，WIFI环境优先使用该链接播放音乐.
     *
     * @var
     */
    public $HQMusicUrl;
    /**
     * 缩略图的媒体id，通过素材管理中的接口上传多媒体文件，得到的id.
     *
     * @var
     */
    public $ThumbMediaId;
}

/**
 * 图文消息-图文元素.
 */
class WxArticles
{
    /**
     * 图文消息-图文消息项.
     *
     * @var
     */
    public $item;
}

/**
 * 图文消息-图文明细项元素.
 */
class WxArticlesItem
{
    /**
     * 图文消息标题.
     *
     * @var
     */
    public $Title;
    /**
     * 图文消息描述.
     *
     * @var
     */
    public $Description;
    /**
     * 图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200.
     *
     * @var
     */
    public $PicUrl;
    /**
     * 点击图文消息跳转链接.
     *
     * @var
     */
    public $Url;
}
