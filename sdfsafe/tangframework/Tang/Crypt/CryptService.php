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
namespace Tang\Crypt;
use Tang\Services\ServiceProvider;

/**
 * 加密服务
 * Class CryptService
 * @package Tang\Crypt
 */
class CryptService extends ServiceProvider
{
    /**
     * @return \Tang\Manager\IManager
     */
    public static function getService()
    {
        return parent::getService();
    }
    protected static function register()
    {
        $config = static::$config->replaceGet('crypt',array('defaultDriver' => 'aes','key'=>'','iv'=>''));
        $instance = static::initObject('crypt','\Tang\Manager\IManager');
        $instance->setConfig($config);
        return $instance;
    }
}