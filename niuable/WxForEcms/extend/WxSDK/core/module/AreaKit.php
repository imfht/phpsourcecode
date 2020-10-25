<?php
namespace WxSDK\core\module;

use WxSDK\core\common\IApp;
use WxSDK\Request;
use WxSDK\core\model\Model;
use WxSDK\Url;
use WxSDK\resource\Config;

class AreaKit
{
    public function getAreaInfo(IApp $app){
        $request = new Request($app, new Model(), new Url(Config::$get_area_info));
        return $request->run();
    }
}

