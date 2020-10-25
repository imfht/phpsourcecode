<?php
namespace WxSDK\core\module;

include __DIR__."/../lib/wxKey/wxBizMsgCrypt.php";

use WxSDK\core\common\IReply;
use WxSDK\core\common\IApp;
use WxSDK\core\model\AppModel;
use WxSDK\core\model\ReplyMsg;
use WxSDK\core\model\WxMsg;
use WxSDK\core\model\msg\Text;
use WxSDK\core\model\msg\Image;
use WxSDK\core\model\msg\Voice;
use WxSDK\core\model\msg\Video;
use WxSDK\core\model\msg\ShortVideo;
use WxSDK\core\model\msg\Location;
use WxSDK\core\model\msg\Link;
use WxSDK\core\model\msg\Event;
use WxSDK\core\model\msg\ScanCodeInfo;
use WXBizMsgCrypt;

/**
 * 自动回复API
 * @author 王维
 *
 */
class AutoReply
{
        public $iReply;
        public $iApp;
        function __construct(IReply $iReply,IApp $iApp){
            $this->iReply = $iReply;
            $this->iApp = $iApp;
        }
        /**
         *
         * @param string $key 从$_GET中获取微信id的键名
         */
        public function start(){
            $appModel = $this->iApp->getModel();
            if(null == $appModel){
                echo "本地配置错误！";
                exit();
            }
            $this->valid4First($appModel->token);
            $pc = null;
            // 判断是否加密，若加密，则重新给$pc赋值
            if (isset ( $_GET ['encrypt_type'] ) && ($_GET ['encrypt_type'] == 'aes')) {
                $pc = $this->getPc($appModel);
            }
            $wxMsg = $this->getInputContent($pc);

            $replyMsg = $this->iReply->getReplyMsg($wxMsg,$this->iApp);
            $this->sendReply($replyMsg, $pc);
            $this->iReply->afterReply($wxMsg, $replyMsg);
        }

        private function sendReply(ReplyMsg $replyMsg, WXBizMsgCrypt $pc = NULL){
            $errCode = 0;
            if($replyMsg->encrypt){
                $encryptMsg = "";
                $errCode = $pc->encryptMsg($replyMsg->msg, $replyMsg->timeStamp, $replyMsg->nonce, $encryptMsg);
                if(0 == $errCode){
                    echo $encryptMsg;
                }else{
                    echo "";
                }
            }else{
                echo $replyMsg->msg;
            }
            $replyMsg->errCode = $errCode;
        }

    /**
                * 双向验证
     * 在微信公众号官方管理后台填入URL和Token时，必须进入此方法验证。
     * @param string $token 口令，注意与access_token区别
     */
    private function valid4First(string $token) {
        // 第一次在微信端填写URL和Token时会验证
        if (isset ( $_GET ["echostr"] ) && $_GET ['echostr']) {
            $echoStr = $_GET ["echostr"];
            // valid signature , option
            if (self::checkSignature ($token)) {
                echo $echoStr;
                exit ();
            } else {
                echo '验证失败';
                exit ();
            }
        }
    }
    /**
     * 获取输入的数据（微信服务器传递的数据）
     * @param WXBizMsgCrypt 微信加解密实例
     * @return \WxSDK\core\model\WxMsg 解析后的信息
     */
    protected function getInputContent(WXBizMsgCrypt $pc=null) {
        // 		$postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];
        //替换为更强大的“方法”获取输入的数据，防止部分服务器中由于php.ini设置不能正常获取的情况
        $postStr = file_get_contents("php://input");
        if (empty ( $postStr )) {
            return new WxMsg();
        } else {
            $encrypt = false;
            $timestamp = null;
            $nonce = null;
            if (null != $pc) { // 判断是否加密，若加密，则执行解密
                $encrypt = true;
                $timestamp = $_GET ["timestamp"];
                $nonce = $_GET ["nonce"];
                $msg_signature = $_GET ["msg_signature"];
                $decryptMsg = "";
                $errCode = $pc->decryptMsg ( $msg_signature, $timestamp, $nonce, $postStr, $decryptMsg );
                if ($errCode) {
                    echo $errCode;
                    exit ("解密失败");
                }
                $postStr =  $decryptMsg;
            }

            libxml_disable_entity_loader ( true );
            $postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
            $input = json_encode ( $postObj );
            $input = json_decode ( $input, 1);
            $msg = $this->transMsg($input);
            $msg->encrypt = $encrypt;
            $msg->timeStamp = $timestamp?$timestamp:time();
            $msg->nonce = $nonce?$nonce:$this->getNonce();
            return $msg;
        }
    }
    private function getNonce(){
        //取随机10位字符串
        $strs="QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        $name=substr(str_shuffle($strs),mt_rand(0,strlen($strs)-11),10);
        return $name;
    }
    /**
     * 获取微信加解密实例
     * @param AppModel $appModel
     * @return WXBizMsgCrypt
     */
    private function getPc(AppModel $appModel){
        return new WXBizMsgCrypt ( $appModel->token, $appModel, $appModel->appId );
    }
    private function transMsg($mixed){
        $msg = new WxMsg();
        $msgType = $mixed["MsgType"];

        if("text" == $msgType){
            $text = new Text();
            $text->content = $mixed["Content"];
            $text->bizMsgMenuid = isset($mixed["bizmsgmenuid"])?$mixed["bizmsgmenuid"]:null;
            $msg->text = $text;
        }else if("image" == $msgType){
            $image = new Image();
            $image->mediaId = isset($mixed["MediaId"])?$mixed["MediaId"]:null;
            $image->picUrl = $mixed["PicUrl"];
            $msg->image = $image;
        }else if("voice" == $msgType){
            $voice = new Voice();
            $voice->format = $mixed["Format"];
            $voice->mediaId = $mixed["MediaId"];
            $voice->recognition = isset($mixed["Recognition"])?$mixed["Recognition"]:null;
            $msg->voice = $voice;
        }else if("video" == $msgType){
            $video = new Video();
            $video->mediaId = $mixed["MediaId"];
            $video->thumbMediaId = $mixed["ThumbMediaId"];
            $msg->video = $video;
        }else if("shortvideo" == $msgType){
            $shortVideo = new ShortVideo();
            $shortVideo->mediaId = $mixed["MediaId"];
            $shortVideo->thumbMediaId = $mixed["ThumbMediaId"];
            $msg->shortVideo = $shortVideo;
        }else if("location" == $msgType){
            $location = new Location();
            $location ->label = $mixed["Label"];
            $location->locationX = $mixed["Location_X"];
            $location->locationY = $mixed["Location_Y"];
            $location->scale = $mixed["Scale"];
            $msg->location = $location;
        }else if("link" == $msgType){
            $link = new Link();
            $link->description = $mixed["Description"];
            $link->title = $mixed["Title"];
            $link->url = $mixed["Url"];
            $msg->link = $link;
        }else if("event" == $msgType){
            $event = new Event();
            $event->event = $mixed["Event"];
            $event->eventKey = isset($mixed["EventKey"])?$mixed["EventKey"]:null;
            $event->latitude = isset($mixed["Latitude"])?$mixed["Latitude"]:null;
            $event->longitude = isset($mixed["Longitude"])?$mixed["Longitude"]:null;
            $event->precision = isset($mixed["Precision"])?$mixed["Precision"]:null;
            $event->ticket = isset($mixed["Ticket"])?$mixed["Ticket"]:null;
            $event->menuID = isset($mixed["MenuId"])?$mixed["MenuId"]:null;

            if($mixed["ScanCodeInfo"]){
                $scanCodeInfo = new ScanCodeInfo();
                $scanCodeInfo->scanType = isset($mixed["ScanCodeInfo"]["ScanType"])?$mixed["ScanCodeInfo"]["ScanType"]:null;
                $scanCodeInfo->scanResult = isset($mixed["ScanCodeInfo"]["ScanResult"])?$mixed["ScanCodeInfo"]["ScanResult"]:null;
                $event->scanCodeInfo = $scanCodeInfo;
            }
            //实测菜单的操作效果，发现没有下面的推送
//             elseif ($mixed["SendPicsInfo"]){
//                 $count = $mixed["SendPicsInfo"]["Count"]?? 0;
//                 $sendPicsInfo = new SendPicsInfo($count,[]);
//                 $list = $mixed["SendPicsInfo"]["PicList"];
//                 $items = [];
//                 foreach ($list as $v){
//                     $items[] = new SendPicsInfItem($v["item"]["PicMd5Sum"]);
//                 }
//                 $sendPicsInfo->picList=$items;
//             }
//             elseif($mixed["SendLocationInfo"]){
//                 $sendLocationInfo = new SendLocationInfo();
//                 $sendLocationInfo->label = $mixed["SendLocationInfo"]["Label"];
//                 $sendLocationInfo->locationX = $mixed["SendLocationInfo"]["Location_X"];
//                 $sendLocationInfo->Location_Y = $mixed["SendLocationInfo"]["Location_Y"];
//                 $sendLocationInfo->scale = $mixed["SendLocationInfo"]["Scale"];
//                 $sendLocationInfo->poiname = $mixed["SendLocationInfo"]["Poiname"]??null;
//             }

            $msg->event = $event;
        }
        $msg->createTime = $mixed["CreateTime"];
        $msg->fromUserName = $mixed["FromUserName"];
        $msg->toUserName = $mixed["ToUserName"];
        $msg->msgType = $msgType;
        return $msg;
    }

    /**
     * checkSignature
                     * 验证令牌
     * @return boolean 验证结果，通过则返回真
     */
    private static function checkSignature(string $token) {
        $signature = $_GET ["signature"]?$_GET ["signature"]:"";
        $timestamp = $_GET ["timestamp"]?$_GET ["timestamp"]:"";
        $nonce = $_GET ["nonce"]?$_GET ["nonce"]:"";
        $tmpArr = array (
            $token,
            $timestamp,
            $nonce
        );
        // use SORT_STRING rule
        sort ( $tmpArr, SORT_STRING );
        $tmpStr = implode ( $tmpArr );
        $tmpStr = sha1 ( $tmpStr );
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

}

