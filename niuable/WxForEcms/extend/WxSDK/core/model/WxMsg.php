<?php
namespace WxSDK\core\model;

use WxSDK\core\model\msg\Event;
use WxSDK\core\model\msg\Link;
use WxSDK\core\model\msg\Location;
use WxSDK\core\model\msg\ShortVideo;
use WxSDK\core\model\msg\Text;
use WxSDK\core\model\msg\Image;
use WxSDK\core\model\msg\Voice;
use WxSDK\core\model\msg\Video;

class WxMsg
{
    public $timeStamp;
    public $nonce;
    public $toUserName;
    public $fromUserName;
    public $createTime;
    public $msgType;
    public $msgId;
    public $text;
    public $image;
    public $voice;
    public $video;
    public $shortVideo;
    public $location;
    public $link;
    public $encrypt;
    public $event;
    function __construct(Text $text = null, Image $image = null, Voice $voice = null,Video $video = null,
                         ShortVideo $shortVideo = null, Location $location = null, Link $link = null,
                         Event $event=null, bool $encrypt = null){
        $this->text = $text;
        $this->image = $image;
        $this->voice = $voice;
        $this->video = $video;
        $this->shortVideo = $shortVideo;
        $this->location = $location;
        $this->link = $link;
        $this->event = $event;
        $this->encrypt = $encrypt ? $encrypt : false;
    }
    
    public function isEvent(){
        return $this->msgType == "event";
    }
    
    public function isClickEvent(){
        return $this->msgType == "event" && $this->event->event == "CLICK";
    }
    
    public function isImage(){
        return $this->msgType == "image";
    }
    public function isVoice(){
        return $this->msgType == "voice";
    }
    public function isVideo(){
        return $this->msgType == "video";
    }
    public function isShortVideo(){
        return $this->msgType == "shortVideo";
    }
    
    public function isText(){
        return $this->msgType == "text";
    }
    
    /**
     * 是否为选择地址上报信息
     * @return boolean
     */
    public function isLocation(){
        return $this->msgType == "location";
    }
    /**
     * 是否为自动上报地理位置时间，
     * 一般在进入公众号对话界面时发生
     * @return boolean
     */
    public function isLocationEvent(){
        return $this->msgType == "event" && $this->event->event == "LOCATION";
    }
}