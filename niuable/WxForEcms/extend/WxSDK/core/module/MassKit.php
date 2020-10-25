<?php
namespace WxSDK\core\module;

use WxSDK\core\common\IApp;
use WxSDK\core\model\mass\Mass;
use WxSDK\core\model\mass\PreviewMass;
use WxSDK\core\model\Model;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;
use WxSDK\Url;
use WxSDK\Request;

class MassKit
{
    public static function sendPreview(IApp $App, PreviewMass $previewMass){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$mass_preview);
//             $json = json_encode($previewMass,JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url,$previewMass);
        }else{
            return $ret;
        }
    }
    public static function sendMassByTag(IApp $App, Mass $mass){
        $url = new Url(Config::$do_mass_by_tag);
        $request = new Request($App, $mass, $url);
        return $request->run();
    }
    
    public static function sendMassByIds(IApp $App, Mass $mass){
        $url = new Url(Config::$do_mass_by_ids);
        $request = new Request($App, $mass, $url);
        
        return $request->run();
    }
    public static function deleteMass(IApp $App, $msg_id, $article_idx){
        $url = new Url(Config::$delete_mass);
        $data = [
            "msg_id"=>$msg_id,
            "article_idx" => $article_idx
        ];
        $model = new Model($data);
        $request = new Request($App, $model, $url);
        
        return $request->run();
    }
    /**
     * 
     * @param IApp $App
     * @return \WxSDK\core\common\Ret data:{ "speed":3, "realspeed":15 }
     */
    public static function getMassSpeed(IApp $App){
        $url = new Url(Config::$get_mass_speed);
        $request = new Request($App, new Model(), $url);
        
        return $request->run();
    }
    /**
     * 
     * @param IApp $App
     * @param int $speed 群发速度的级别
     * speed	realspeed
        * 0	80w/分钟
        * 1	60w/分钟
        * 2	45w/分钟
        * 3	30w/分钟
        * 4	10w/分钟
     * @return \WxSDK\core\common\Ret
     */
    public static function setMassSpeed(IApp $App, int $speed){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$set_mass_speed);
            $data = ["speed"=>$speed];
            return Tool::doCurl($url,$data);
        }else{
            return $ret;
        }
    }
    public static function getMassStatus(IApp $App, $msg_id){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$get_mass_status);
            $data = ["msg_id"=>$msg_id];
            $ret = Tool::doCurl($url,$data);
            if($ret->ok()){
                $data = $ret->getData();
                $ret->data = $data["msg_status"]?$data["msg_status"]:"";
            }
            return $ret;
        }else{
            return $ret;
        }
    }
    /**
     * 打开已群发图文的评论
     * @param IApp $App
     * @param string $msg_data_id 群发返回的msg_data_id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认操作该msg_data_id的第一篇图文
     * @return \WxSDK\core\common\Ret
     */
    public static function openMassComment(IApp $App, string $msg_data_id, int $index=null){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$mass_open_comment);
            $data = array(
                "msg_data_id" =>$msg_data_id
            );
            if(null != $index){
                $data["index"] = $index;
            }
//             $json = json_encode($data,JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url,$data);
        }else{
            return $ret;
        }
    }
    /**
     * 关闭已群发图文的评论
     * @param IApp $App
     * @param string $msg_data_id 群发返回的msg_data_id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认操作该msg_data_id的第一篇图文
     * @return \WxSDK\core\common\Ret
     */
    public static function closeMassComment(IApp $App, string $msg_data_id, int $index=null){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$mass_close_comment);
            $data = array(
                "msg_data_id" =>$msg_data_id
            );
            if(null != $index){
                $data["index"] = $index;
            }
//             $json = json_encode($data,JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url,$data);
        }else{
            return $ret;
        }
    }
    /**
     * 标记为精选评论
     * @param IApp $App
     * @param string $msg_data_id
     * @param string $user_comment_id 用户评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认操作该msg_data_id的第一篇图文
     * @return \WxSDK\core\common\Ret
     */
    public static function markelectComment(IApp $App, string $msg_data_id, string $user_comment_id, int $index=null){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$mass_comment_markelect);
            $data = array(
                "msg_data_id" =>$msg_data_id,
                "user_comment_id" =>$user_comment_id,
            );
            if(null != $index){
                $data["index"] = $index;
            }
//             $json = json_encode($data,JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url,$data);
        }else{
            return $ret;
        }
    }

    /**
     * 取消标记为精选评论
     * @param IApp $App
     * @param string $msg_data_id
     * @param string $user_comment_id 用户评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认操作该msg_data_id的第一篇图文
     * @return \WxSDK\core\common\Ret
     */
    public static function unMarkelectComment(IApp $App, string $msg_data_id, string $user_comment_id, int $index=null){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$mass_comment_unmarkelect);
            $data = array(
                "msg_data_id" =>$msg_data_id,
                "user_comment_id" =>$user_comment_id,
            );
            if(null != $index){
                $data["index"] = $index;
            }
//             $json = json_encode($data,JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url,$data);
        }else{
            return $ret;
        }
    }
    /**
     * 查看指定文章的评论数据
     * @param IApp $App
     * @param string $msg_data_id 群发返回的msg_data_id
     * @param int $begin 起始位置
     * @param int $count 获取数目（>=50会被拒绝）
     * @param int $type type=0 普通评论&精选评论 type=1 普通评论 type=2 精选评论
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认操作该msg_data_id的第一篇图文
     * @return \WxSDK\core\common\Ret
     */
    public static function getMassComment(IApp $App, string $msg_data_id, int $begin, int $count, int $type, int $index=null){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$mass_get_comment);
            $data = array(
                "msg_data_id" =>$msg_data_id,
                "begin" =>$begin,
                "count" => $count,
                "type" => $type
            );
            if(null != $index){
                $data["index"] = $index;
            }
//             $json = json_encode($data,JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url,$data);
        }else{
            return $ret;
        }
    }
    /**
     * 删除评论
     * @param IApp $App
     * @param string $msg_data_id
     * @param string $user_comment_id 用户评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认操作该msg_data_id的第一篇图文
     * @return \WxSDK\core\common\Ret
     */
    public static function deleteComment(IApp $App, string $msg_data_id, string $user_comment_id, int $index=null){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$mass_delete_comment);
            $data = array(
                "msg_data_id" =>$msg_data_id,
                "user_comment_id" =>$user_comment_id,
            );
            if(null != $index){
                $data["index"] = $index;
            }
//             $json = json_encode($data,JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url,$data);
        }else{
            return $ret;
        }
    }
    /**
     * 回复评论
     * @param IApp $App
     * @param string $msg_data_id
     * @param string $user_comment_id
     * @param string $content 回复的内容
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认操作该msg_data_id的第一篇图文
     * @return \WxSDK\core\common\Ret
     */
    public static function replyComment(IApp $App, string $msg_data_id, string $user_comment_id, string $content, int $index=null){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$mass_reply_comment);
            $data = array(
                "msg_data_id" =>$msg_data_id,
                "user_comment_id" =>$user_comment_id,
                "content"=>$content
            );
            if(null != $index){
                $data["index"] = $index;
            }
//             $json = json_encode($data,JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url,$data);
        }else{
            return $ret;
        }
    }
    /**
     * 删除评论回复
     * @param IApp $App
     * @param string $msg_data_id
     * @param string $user_comment_id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认操作该msg_data_id的第一篇图文
     * @return \WxSDK\core\common\Ret
     */
    public static function replyCommentDelete(IApp $App, string $msg_data_id, string $user_comment_id, int $index=null){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$mass_reply_comment_delete);
            $data = array(
                "msg_data_id" =>$msg_data_id,
                "user_comment_id" =>$user_comment_id,
            );
            if(null != $index){
                $data["index"] = $index;
            }
//             $json = json_encode($data,JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url,$data);
        }else{
            return $ret;
        }
    }
    public static function createPreviewMpnews($mediaId, string $openid = null, string $wxname = null){
        $previewMass = new PreviewMass();
        $previewMass->msgtype = "mpnews";
        $previewMass->mpnews = array("media_id"=>$mediaId);
        $previewMass->touser = $openid;
        $previewMass->towxname = $wxname;
        return $previewMass;
    }
    
    public static function createPreviewText(string $text, string $openid = null, string $wxname = null){
        $previewMass = new PreviewMass();
        $previewMass->msgtype = "text";
        $previewMass->text = array("content"=>$text);
        $previewMass->touser = $openid;
        $previewMass->towxname = $wxname;
        return $previewMass;
    }
    public static function createPreviewVoice(string $mediaId, string $openid = null, string $wxname = null){
        $previewMass = new PreviewMass();
        $previewMass->msgtype = "voice";
        $previewMass->voice = array("media_id"=>$mediaId);
        $previewMass->touser = $openid;
        $previewMass->towxname = $wxname;
        return $previewMass;
    }
    public static function createPreviewImage(string $mediaId, string $openid = null, string $wxname = null){
        $previewMass = new PreviewMass();
        $previewMass->msgtype = "image";
        $previewMass->image = array("media_id"=>$mediaId);
        $previewMass->touser = $openid;
        $previewMass->towxname = $wxname;
        return $previewMass;
    }
    public static function createPreviewMpVideo(string $mediaId, string $openid = null, string $wxname = null){
        $previewMass = new PreviewMass();
        $previewMass->msgtype = "mpvideo";
        $previewMass->mpvideo = array("media_id"=>$mediaId);
        $previewMass->touser = $openid;
        $previewMass->towxname = $wxname;
        return $previewMass;
    }
    public static function createPreviewWxCard(string $cardId,$cardExt = null, string $openid = null, string $wxname = null){
        $previewMass = new PreviewMass();
        $previewMass->msgtype = "wxcard";
        if(null == $cardExt){
            $previewMass->wxcard = array("card_id"=>$cardId);
        }else{
            $previewMass->wxcard = array(
                "card_id"=>$cardId,
                "card_ext"=>$cardExt
            );
        }
        $previewMass->touser = $openid;
        $previewMass->towxname = $wxname;
        return $previewMass;
    }
}

