<?php
namespace WxSDK\core\module\smart;

use WxSDK\Request;
use WxSDK\Url;
use WxSDK\core\common\IApp;
use WxSDK\core\model\smart\MeaningInfo;
use WxSDK\resource\Config;

class MeanKit
{
    public static function  guessMeaning(IApp $app, MeaningInfo $model){
        $request = new Request($app, $model, new Url(Config::$smart_guess_meaning));
        return $request->run();
    }
}

