<?php
namespace WxSDK\core\module;

use WxSDK\core\common\IApp;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;

class ReplyKit
{
    public static function getAutoReplyRules(IApp $App) {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$reply_get_rules);
            return Tool::doCurl($url);
        } else {
            return $ret;
        }
    }
}

