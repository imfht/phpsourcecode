<?php
namespace WxSDK\core\model\mass;

use WxSDK\core\model\Model;

class Mass extends Model
{
    /**
     * 
     * @var Filter
     */
    public $filter;
    /**
     * 
     * @var array
     */
    public $touser;
    /**
     * 
     * @var string
     */
    public $msgtype;
    /**
     * 图文消息被判定为转载时，是否继续群发。 1为继续群发（转载），0为停止群发。 该参数默认为0。
     * @var integer | null
     */
    public $send_ignore_reprint;
    /**
     * 
     * @var array
     */
    public $text;
    /**
     * 
     * @var array
     */
    public $voice;
    /**
     * 
     * @var array
     */
    public $image;
    /**
     * 
     * @var array
     */
    public $mpvideo;
    /**
     * 
     * @var array
     */
    public $wxcard;
    /**
     * 
     * @var array
     */
    public $mpnews;
    /**
     * 群发时，微信后台将对 24 小时内的群发记录进行检查，
     * 如果该 clientmsgid 已经存在一条群发记录，则会拒绝本次群发请求，返回已存在的群发msgid，
     * 开发者可以调用“查询群发消息发送状态”接口查看该条群发的状态。
     * @var string
     */
    public $clientmsgid;
    
    public static function createTextMass(string $text, Filter $filter = null, array $touser = null ){
        $mass = new Mass();
        $mass->filter = $filter;
        $mass->touser = $touser;
        $mass->msgtype = "text";
        $mass->text = array("content"=>$text);
        return $mass;
    }
    public static function createVoiceMass(string $mediaId, Filter $filter = null, array $touser = null ){
        $mass = new Mass();
        $mass->filter = $filter;
        $mass->touser = $touser;
        $mass->msgtype = "voice";
        $mass->voice = array("media_id"=>$mediaId);
        return $mass;
    }
    public static function createImageMass(string $mediaId, Filter $filter = null, array $touser = null ){
        $mass = new Mass();
        $mass->filter = $filter;
        $mass->touser = $touser;
        $mass->msgtype = "image";
        $mass->image = array("media_id"=>$mediaId);
        return $mass;
    }

    /**
     * @param string $mediaId
     * @param Filter|null $filter
     * @param array|null $touser
     * @param bool $sendIgnoreReprint 图文消息被判定为转载时，是否继续群发,默认停止群发
     * @return Mass
     */
    public static function createMpnewsMass(string $mediaId, Filter $filter = null, array $touser = null ,bool $sendIgnoreReprint=false){
        $mass = new Mass();
        $mass->filter = $filter;
        $mass->touser = $touser;
        $mass->msgtype = "mpnews";
        $mass->mpnews = array("media_id"=>$mediaId);
        $mass->send_ignore_reprint = $sendIgnoreReprint?1:0;
        return $mass;
    }
    public static function createMpVideoMass(string $mediaId, Filter $filter = null, array $touser = null ){
        $mass = new Mass();
        $mass->filter = $filter;
        $mass->touser = $touser;
        $mass->msgtype = "mpvideo";
        $mass->mpvideo = array("media_id"=>$mediaId);
        return $mass;
    }
    public static function createWxCardMass(string $cardId, Filter $filter = null, array $touser = null ){
        $mass = new Mass();
        $mass->filter = $filter;
        $mass->touser = $touser;
        $mass->msgtype = "wxcard";
        $mass->wxcard = array("card_id"=>$cardId);
        return $mass;
    }
    
    public static function createFilter(bool $isToAll = true, string $tagId = NULL){
        return new Filter($isToAll,$tagId);
    }
}

