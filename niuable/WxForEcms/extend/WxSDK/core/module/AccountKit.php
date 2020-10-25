<?php
namespace WxSDK\core\module;

use WxSDK\Request;
use WxSDK\Url;
use WxSDK\core\common\IApp;
use WxSDK\core\model\Model;
use WxSDK\resource\Config;

class AccountKit
{
    /**
     * 换取短链接
     * @param IApp $accessToken token获取实例
     * @param string $longUrl 长连接
     * @return \WxSDK\core\common\Ret 结果
     */
    public static function urlTrans2Short(IApp $accessToken, string $longUrl) {
        $model = new Model(array(
            "action" =>"long2short",
            "long_url"=>$longUrl
        ));

        $request = new Request($accessToken, $model, new Url(Config::$url_get_short_from_long));
        return $request->run();
    }
}

