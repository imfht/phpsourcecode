<?php
namespace WxSDK\core\model;

use WxSDK\core\model\news\NewItem;

class ReplyMsg
{
    public $msg;
    public $encrypt;
    public $timeStamp;
    public $nonce;
    public $errCode;
    /**
     * 文本消息
     * @param string $toUser
     * @param string $fromUser
     * @param string $content
     * @param int $createTime
     * @return string
     */
    public static function getTextMsg(string $toUser=null, string $fromUser=null, string $content = null , int $createTime = null) {
        $toUser=$toUser ? $toUser : "";
        $fromUser=$fromUser ? $fromUser : "";
        $content = $content ? $content : "";
        $createTime = $createTime ? $createTime : 0;

        $createTime = self::getCreateTime($createTime);
        $tpl = "<xml>
          <ToUserName><![CDATA[%s]]></ToUserName>
          <FromUserName><![CDATA[%s]]></FromUserName>
          <CreateTime>%d</CreateTime>
          <MsgType><![CDATA[text]]></MsgType>
          <Content><![CDATA[%s]]></Content>
        </xml>";
        $str = sprintf($tpl, $toUser, $fromUser, $createTime, $content);
        return $str;
    }
    /**
     * 图片消息
     * @param string $toUser
     * @param string $fromUser
     * @param string $mediaId 通过素材管理中的接口上传多媒体文件，得到的id
     * @param int $createTime
     * @return string
     */
    public static function getImageMsg(string $toUser, string $fromUser, string $mediaId, int $createTime = null) {
        $createTime = $createTime ? $createTime : 0;
        $createTime = self::getCreateTime($createTime);
        $tpl = "<xml>
    		<ToUserName><![CDATA[%s]]></ToUserName>
    		<FromUserName><![CDATA[%s]]></FromUserName>
    		<CreateTime>%s</CreateTime>
    		<MsgType><![CDATA[image]]></MsgType>
    		<Image>
    		<MediaId><![CDATA[%s]]></MediaId>
    		</Image>
		</xml>";
        self::getNewsMsg($toUser, $fromUser, $createTime, new NewItem(),new NewItem());
        return sprintf($tpl,$toUser,$fromUser,$createTime,$mediaId);
    }
    
    /**
     * 图文消息
     * @param string $toUser
     * @param string $fromUser
     * @param string $createTime
     * @param NewItem ...$news 图文消息组
     * @return string
     */ 
    public static function getNewsMsg(string $toUser, string $fromUser, string $createTime, NewItem... $news) {
        $createTime = self::getCreateTime($createTime);
        $headerTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<ArticleCount>%d</ArticleCount>
			<Articles>";
        $bodyTpl = "<item>
			<Title><![CDATA[%s]]></Title>
			<Description><![CDATA[%s]]></Description>
			<PicUrl><![CDATA[%s]]></PicUrl>
			<Url><![CDATA[%s]]></Url>
			</item>"; 
        $footerTpl = "</Articles></xml>";

        $header = sprintf ( $headerTpl, $toUser, $fromUser, $createTime, count ( $news ) );

        $body="";//一定要初始化！且初始化为空字符串
        foreach ( $news as  $v ) {
            $body .= sprintf ( $bodyTpl, $v->title, $v->description, $v->picUrl,$v->url);
        }
        $res = $header . $body . $footerTpl;
        return $res;
    }
    /**
     * 语音消息
     * @param string $toUser
     * @param string $fromUser
     * @param string $mediaId 通过素材管理中的接口上传多媒体文件，得到的id
     * @param int $createTime
     * @return string
     */
    public static function getVoiceMsg(string $toUser,string $fromUser, string $mediaId,int $createTime = null) {
        $createTime = $createTime ? $createTime : 0;
        $createTime = self::getCreateTime($createTime);
        $format = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[voice]]></MsgType>
            <Voice>
            <MediaId><![CDATA[%s]]></MediaId>
            </Voice>
        </xml>";
        return sprintf($format, $toUser, $fromUser, $createTime, $mediaId);
    }
    /**
     * 视频消息
     * @param string $toUser
     * @param string $fromUser
     * @param string $mediaId 通过素材管理中的接口上传多媒体文件，得到的id
     * @param string $title
     * @param string $description
     * @param int $createTime
     * @return string
     */
    public static function getVideoMsg(string $toUser, string $fromUser, string $mediaId, string $title, string $description, int $createTime = null ){
        $createTime = $createTime ? $createTime : 0;
        $createTime = self::getCreateTime($createTime);
        $format = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[video]]></MsgType>
            <Video>
            <MediaId><![CDATA[%s]]></MediaId>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            </Video>
        </xml>";
        return sprintf($format,$toUser,$fromUser,$createTime,$mediaId,$title,$description);
    }
    /**
     * 音乐消息
     * @param string $toUser 用户
     * @param string $fromUser 公众号
     * @param string $title 标题
     * @param string $description 描述
     * @param string $musicUrl 音乐链接
     * @param string $HQMusicUrl 高质量音乐链接，WIFI环境优先使用该链接播放音乐
     * @param string $thumbMediaId 缩略图的媒体id，通过素材管理中的接口上传多媒体文件，得到的id
     * @param int $createTime 消息时间
     * @return string
     */
    public static function getMusicMsg(string $toUser,string $fromUser,string $title,string $description,string $musicUrl,string $HQMusicUrl,string $thumbMediaId,int $createTime = null) {
        $createTime = $createTime ? $createTime : 0;
        $createTime = self::getCreateTime($createTime);
        $format = "<xml>
          <ToUserName><![CDATA[%s]]></ToUserName>
          <FromUserName><![CDATA[%s]]></FromUserName>
          <CreateTime>%s</CreateTime>
          <MsgType><![CDATA[music]]></MsgType>
          <Music>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <MusicUrl><![CDATA[%s]]></MusicUrl>
            <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
            <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
          </Music>
        </xml>";
        return sprintf($format, $toUser, $fromUser, $createTime, $title, $description, $musicUrl, $HQMusicUrl, $thumbMediaId);
    }
    
    private static function getCreateTime($createTime){
        if(empty($createTime)){
            $createTime = time();
        }
        return $createTime;
    }
}

