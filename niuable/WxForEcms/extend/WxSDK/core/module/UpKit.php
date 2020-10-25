<?php

namespace WxSDK\core\module;

use WxSDK\Request;
use WxSDK\Url;
use WxSDK\core\common\IApp;
use WxSDK\core\model\Model;
use WxSDK\core\model\mass\Article;
use WxSDK\core\model\mass\News;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;

class UpKit
{


    /**
     * 上传图片-图文内容中的图片
     * @param IApp $App
     * @param string $filename 文件名，含路径
     * @return \WxSDK\core\common\Ret data包含url数据
     */
    public static function uploadImage4MpnewsContent(IApp $App, string $filename)
    {
        $mediaData = self::createMediaModel($filename);
        $url = new Url(Config::$up_img_for_news_content);
        $request = new Request($App, $mediaData, $url);
        
        return $request->run();
    }


    /**
     *
     * @param IApp $App
     * @param string $type 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @param string $filename 文件名，含路径
     * @param string $title 视频文件时需要，默认解析为文件basename
     * @param string $introduction 视频文件时需要，默认解析为文件basename
     * @return \WxSDK\core\common\Ret
     */
    public static function uploadMedia4Forever(IApp $App, string $type, string $filename, string $title = NULL, string $introduction = NULL)
    {

        $template = str_replace("TYPE", $type, Config::$up_media_forever);
        $url = new Url($template);
        
        if ($type == "video") {
            $title = $title ? $title : basename($filename);
            $introduction = $introduction ? $introduction : basename($filename);
            $mediaData = self::createMedia4VideoModel($filename, $title, $introduction);
        } else {
            $mediaData = self::createMediaModel($filename);
        }
        $request = new Request($App, $mediaData, $url);
        
        return $request->run();
    }

    /**
     * 上传图文封面图片获取thumb_media_id
     * @param IApp $App
     * @param string $filename
     * @return \WxSDK\core\common\Ret
     */
    public static function uploadPicture4NewsCover(IApp $App, string $filename)
    {
        $mediaData = self::createMediaModel($filename);
        $template = str_replace("TYPE", "image", Config::$up_media_forever);
        $url = new Url($template);
        $request = new Request($App, $mediaData, $url);
        
        return $request->run();
    }

    public static function uploadNews4Mass(IApp $App, News $news)
    {
        $url = new Url(Config::$up_news_for_mass);
        $request = new Request($App, $news, $url);
        
        return $request->run();
    }

    public static function uploadVideo4Mass(IApp $App, string $mediaId, $title = "", $description = "")
    {
        $url = new Url(Config::$up_video_for_mass);
        $mediaData = new Model(array(
            "media_id" => $mediaId,
            "title" => $title,
            "description" => $description
        ));
        $request = new Request($App, $mediaData, $url);
        
        return $request->run();
    }

    public static function uploadNewsForever(IApp $App, News $news)
    {
        $url = new Url(Config::$up_news_forever);

        $request = new Request($App, $news, $url);
        
        return $request->run();
    }

    /**
     * 更新永久图文
     * @param IApp $App
     * @param Article $article
     * @param string $mediaId
     * @param int $index
     * @return \WxSDK\core\common\Ret
     */
    public static function updateNewsForever(IApp $App, Article $article, string $mediaId, int $index)
    {
        $url = new Url(Config::$update_news_forever);
        $mediaData = new Model(array(
            "media_id" => $mediaId,
            "index" => $index,
            "articles" => $article
        ));
        $request = new Request($App, $mediaData, $url);
        
        return $request->run();
    }

    /**
     *
     * @param IApp $App
     * @param string $type 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @param string $filename 文件名，含路径
     * @return \WxSDK\core\common\Ret
     */
    public static function uploadMedia4ShortTime(IApp $App, string $type, string $filename, $description = [])
    {
        $template = str_replace("TYPE", $type, Config::$up_media_short_time);
        $url = new Url($template);
        $model = self::createMediaModel($filename, $description);
        $request = new Request($App, $model, $url);
        return $request->run();
    }


    private static function createMediaModel(String $filename, $description=[])
    {
        if(empty($description)){
            return new Model(Tool::createMediaData($filename),TRUE);
        }else{
            $array = Tool::createMediaData($filename);
            $array['description'] = $description;
            return new Model($array, TRUE);
        }
    }

    private static function createMedia4VideoModel(String $filename, string $title, string $introduction)
    {
        $a = array(
            'title' => $title,
            'introduction' => $introduction
        );
        $a = json_encode($a, JSON_UNESCAPED_UNICODE);
        if (class_exists('\CURLFile')) {//关键是判断curlfile,官网推荐php5.5或更高的版本使用curlfile来实例文件
            $mediaData = array(
                'media' => new \CURLFile (realpath($filename)),
                'description' => $a
            );
        } else {
            $mediaData = array(
                'media' => '@' . realpath($filename),
                'description' => $a
            );
        }
        return new Model($mediaData, TRUE);
    }
    

}




