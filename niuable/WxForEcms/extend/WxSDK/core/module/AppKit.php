<?php


namespace WxSDK\core\module;


use WxSDK\core\common\IApp;
use WxSDK\core\model\AppModel;
use WxSDK\core\model\Model;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;
use WxSDK\Request;
use WxSDK\Url;

class AppKit
{
    /**
     * 获取微信服务器的ip列表
     * @param IApp $accessToken
     * @return \WxSDK\core\common\Ret data：{    "ip_list": [        "127.0.0.1",         "127.0.0.2",         "101.226.103.0/25"    ]}
     */
    public static function getWxIps(IApp $accessToken)
    {
        $ret = $accessToken->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$app_get_ips);
            return Tool::doCurl($url);
        } else {
            return $ret;
        }
    }

    /**
     * 从微信服务器远程获取access_token
     * @param AppModel $appModel
     * @return \WxSDK\core\common\Ret data:{"access_token":"ACCESS_TOKEN","expires_in":7200}
     */
    public static function getAccessToken(AppModel $appModel)
    {
        $url = str_replace("APPID", trim($appModel->getAppId()), Config::$app_get_access_token);
        $url = str_replace("APPSECRET", trim($appModel->getAppSecret()), $url);
        return Tool::doCurl($url);
    }

    /**
     * 检查网络
     * @param IApp $accessToken
     * @param string $action 执行的检测动作，允许的值：dns（做域名解析）、ping（做ping检测）、all（dns和ping都做）
     * @param string $checkOperator 指定平台从某个运营商进行检测，允许的值：CHINANET（电信出口）、UNICOM（联通出口）、CAP（腾讯自建出口）、DEFAULT（根据ip来选择运营商）
     * @return \WxSDK\core\common\Ret
     */
    public static function checkNetwork(IApp $accessToken, string $action = null, string $checkOperator = null)
    {
        $action = $action ? $action : "all";
        $checkOperator = $checkOperator ? $checkOperator :"DEFAULT";
        $data = array(
            "action" => $action,
            "check_operator" => $checkOperator
        );
        $model = new Model($data);
        
        $request = new Request($accessToken, $model, new Url(Config::$app_network_check));
        return $request->run();
    }
}