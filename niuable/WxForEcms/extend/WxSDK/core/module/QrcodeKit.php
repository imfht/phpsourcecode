<?php
namespace WxSDK\core\module;

use WxSDK\core\common\IApp;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;
use WxSDK\core\model\Model;
use WxSDK\core\common\Ret;
use WxSDK\Request;
use WxSDK\Url;

class QrcodeKit
{
    /**
     * 创建短期二维码
     * @param IApp $App
     * @param int $expireSeconds 该二维码有效时间，以秒为单位。 最大不超过2592000（即30天），此字段如果不填，则默认有效期为30秒。
     * @param int $sceneId 场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
     * @param string $sceneStr 场景值ID（字符串形式的ID），字符串类型，长度限制为1到64
     * @return \WxSDK\core\common\Ret data:
     * {"ticket":"gQH47joAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL2taZ2Z3TVRtNzJXV1Brb3ZhYmJJAAIEZ23sUwMEmm
3sUw==","expire_seconds":60,"url":"http://weixin.qq.com/q/kZgfwMTm72WWPkovabbI"}
     */
    public static function qrcodeCreate4ShortTime(IApp $App
        , int $expireSeconds = null
        , int $sceneId = NULL
        , string $sceneStr = NULL)
    {
        if (null == $sceneId) {
            $action_name = "QR_STR_SCENE";
            $scene = array(
                "scene_str" => $sceneStr
            );
        } else {
            $action_name = "QR_SCENE";
            $scene = array(
                "scene_id" => $sceneId
            );
        }
        $data = array(
            "expire_seconds" => $expireSeconds,
            "action_name" => $action_name,
            "action_info" => array(
                "scene" => $scene
            )
        );
        $model = new Model($data);
        
        $request = new Request($App, $model, new Url(Config::$qrcode_create));
        return $request->run();
    }

    /**
     *
     * @param IApp $App
     * @param int $sceneId 场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
     * @param string $sceneStr 场景值ID（字符串形式的ID），字符串类型，长度限制为1到64
     * @return \WxSDK\core\common\Ret data:
     * {"ticket":"gQH47joAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL2taZ2Z3TVRtNzJXV1Brb3ZhYmJJAAIEZ23sUwMEmm
3sUw==","url":"http://weixin.qq.com/q/kZgfwMTm72WWPkovabbI"}
     * 其中url可以用于自己生成二维码图片，效果与通过qrcodeGetUrlByTicket获取的图片链接一致
     */
    public static function qrcodeCreate4Forever(IApp $App
        , int $sceneId = NULL
        , string $sceneStr = NULL)
    {   
        if (null == $sceneId) {
            $action_name = "QR_LIMIT_STR_SCENE";
            if(!$sceneStr){
                return new Ret('',['errcode'=>1,'errmsg'=>'参数设置错误']);
            }
            $scene = array(
                "scene_str" => $sceneStr
            );
        } else {
            $action_name = "QR_LIMIT_SCENE";
            $scene = array(
                "scene_id" => $sceneId
            );
        }
        $data = array(
            "action_name" => $action_name,
            "action_info" => array(
                "scene" => $scene
            )
        );
        
        $model = new Model($data);
        
        $request = new Request($App, $model, new Url(Config::$qrcode_create));
        return $request->run();
    }

    public static function getUrlByTicket(string $ticket)
    {
        return str_replace("TICKET", urlencode($ticket), Config::$qrcode_create_by_ticket);
    }
}