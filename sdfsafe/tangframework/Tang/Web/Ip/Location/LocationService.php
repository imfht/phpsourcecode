<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Web\Ip\Location;
use Tang\Services\ServiceProvider;

class LocationService extends ServiceProvider
{
    /**
     * @return \Tang\Web\Ip\Location\ILocationManager
     */
    public static function getService()
    {
        return parent::getService();
    }
    protected static function register()
    {
        $config = static::$config->replaceGet('ipLocation',array('defaultDriver'=>'QQWry','QQWryDataPath'=>'qqwry.dat'));
        $instance = static::initObject('ipLocation','\Tang\Web\Ip\Location\ILocationManager');
        $instance->setConfig($config);
        return $instance;
    }
}