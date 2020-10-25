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
namespace Tang\ThirdParty;
use Tang\Services\ServiceProvider;

/**
 * 第三方服务
 * Class ThirdPartyService
 * @package Tang\ThirdParty
 */
class ThirdPartyService extends ServiceProvider
{
    /**
     * @return \Tang\ThirdParty\IThirdParty
     */
    public static function getService()
    {
        return parent::getService();
    }
    protected static function register()
    {
        $instance =  parent::initObject('thirdParty','\Tang\ThirdParty\IThirdParty');
        $instance->setAppDirectory(static::$config->get('applicationDirectory'));
        $instance->setFrameworkDirectory(static::$config->get('frameworkDirectory'));
        return $instance;
    }
}