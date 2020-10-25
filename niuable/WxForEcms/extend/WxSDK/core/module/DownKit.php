<?php
namespace WxSDK\core\module;

use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;
use WxSDK\core\common\IApp;

class DownKit
{
    /**
     *
     * @param IApp $App
     * @param string $mediaId 上传文件时微信返回的media_id
     * @param string $downloadPathName
     * @return \WxSDK\core\common\Ret|mixed
     */
    public static function downloadVideoMedia4ShortTime(IApp $App, string $mediaId, string $downloadPathName){
        $ret = self::getVideoMediaUrl4ShortTime($App, $mediaId);
        if($ret->ok()){
            $url = $ret->getData();
            $res = Tool::download($url, $downloadPathName);
            return $res;
        }else{
            return $ret;
        }
    }
    
    /**
     * 获取视频url
     * @param IApp $App
     * @param string $mediaId 上传文件时微信返回的media_id
     * @return \WxSDK\core\common\Ret
     */
    public static function getVideoMediaUrl4ShortTime(IApp $App, string $mediaId){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$get_media_short_time);
            $url = str_replace("MEDIA_ID",$mediaId,$url);
            $url = str_replace("https:", "http:", $url);
            $ret = Tool::doCurl($url);
            if($ret->ok()){
                $url = $ret->getData()["video_url"];
                $ret->data = $url;
                return $ret;
            }
            return $ret;
        }else{
            return $ret;
        }
    }
    
    /**
     * 获取非视频文件
     * @param IApp $App
     * @param string $mediaId
     * @return \WxSDK\core\common\Ret
     */
    public static function downloadOtherMedia4ShortTime(IApp $App, string $mediaId, string $downloadPathName){
        $ret = self::getOtherMediaUrl4ShortTime($App, $mediaId);
        if($ret->ok()){
            $url = $ret->data;
            $ret = Tool::download($url, $downloadPathName);
            return $ret;
        }else{
            return $ret;
        }
    }
    
    /**
     * 获取非视频文件url
     * @param IApp $App
     * @param string $mediaId
     * @return \WxSDK\core\common\Ret
     */
    public static function getOtherMediaUrl4ShortTime(IApp $App, string $mediaId){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$get_media_short_time);
            $url = str_replace("MEDIA_ID",$mediaId,$url);
            $ret->data = $url;
            return $ret;
        }else{
            return $ret;
        }
    }
    /**
     * 获取非视频、图文的url
     * @param IApp $App
     * @param string $mediaId
     * @return \WxSDK\core\common\Ret
     */
    public static function getOtherMediaUrl4Forever(IApp $App, string $mediaId){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$get_media_forever);
            $url = str_replace("MEDIA_ID",$mediaId,$url);
            $ret->data = $url;
            return $ret;
        }else{
            return $ret;
        }
    }
    /**
     * 获取非视频文件
     * @param IApp $App
     * @param string $mediaId
     * @return \WxSDK\core\common\Ret
     */
    public static function downloadOtherMedia4Forever(IApp $App, string $mediaId, string $downloadPathName){
        $ret = self::getOtherMediaUrl4Forever($App, $mediaId);
        if($ret->ok()){
            $url = $ret->data;
            $ret = Tool::download($url, $downloadPathName);
            return $ret;
        }else{
            return $ret;
        }
    }
    /**
     * 获取视频url
     * @param IApp $App
     * @param string $mediaId 上传文件时微信返回的media_id
     * @return \WxSDK\core\common\Ret data为数组:
     * {
          * "title":TITLE,
          * "description":DESCRIPTION,
          * "down_url":DOWN_URL,
        * }
     */
    public static function getVideoMedia4Forever(IApp $App, string $mediaId){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$get_media_forever);
            $data = array("media_id"=>$mediaId);
//             $data = json_encode($data);
            $ret = Tool::doCurl($url,$data);
            if($ret->ok()){
                return $ret;
            }
            return $ret;
        }else{
            return $ret;
        }
    }
    
    /**
     *
     * @param IApp $App
     * @param string $mediaId 上传文件时微信返回的media_id
     * @param string $downloadPathName
     * @return \WxSDK\core\common\Ret
     */
    public static function downloadVideoMedia4Forever(IApp $App, string $mediaId, string $downloadPathName){
        $ret = self::getVideoMedia4Forever($App, $mediaId);
        if($ret->ok()){
            $url = $ret->getData()["down_url"];
            $res = Tool::download($url, $downloadPathName);
            return $res;
        }else{
            return $ret;
        }
    }
    /**
     * 获取图文
     * 微信返回的为json，不用下载
     * @param IApp $App
     * @param string $mediaId
     * @return \WxSDK\core\common\Ret data为数组结构
     */
    public static function getNewsMedia4Forever(IApp $App, string $mediaId){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$get_media_forever);
            $data = array("media_id"=>$mediaId);
//             $data = json_encode($data);
            $ret = Tool::doCurl($url,$data);
            if($ret->ok()){
                return $ret;
            }
            return $ret;
        }else{
            return $ret;
        }
    }
    
    /**
     * 获取视频url
     * @param IApp $App
     * @param string $mediaId 上传文件时微信返回的media_id
     * @return \WxSDK\core\common\Ret
     */
    public static function deleteMedia4Forever(IApp $App, string $mediaId){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$del_media_forever);
            $data = array("media_id"=>$mediaId);
//             $data = json_encode($data);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }
    
    /**
     *
     * @param IApp $App
     * @return \WxSDK\core\common\Ret data为数组
     * voice_count	语音总数量
     * video_count	视频总数量
     * image_count	图片总数量
     * news_count	图文总数量
     */
    public static function getMediaTotal4Forever(IApp $App){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$get_media_total_forever);
            $ret = Tool::doCurl($url);
            return $ret;
        }else{
            return $ret;
        }
    }
    /**
     * 
     * @param IApp $App
     * @param string $type 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param int $offset 从全部素材的该偏移位置开始返回，0表示从第一个素材 返回
     * @param int $count 返回素材的数量，取值在1到20之间
     * @return \WxSDK\core\common\Ret
     */
    public static function getMediaList4Forever(IApp $App, string $type, int $offset, int $count){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$get_media_list_forever);
            $data = array("type"=>$type,"offset"=>$offset,"count"=>$count);
//             $data=json_encode($data);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }
}

