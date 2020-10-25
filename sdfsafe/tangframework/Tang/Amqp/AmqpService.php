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
namespace Tang\Amqp;
use Tang\Services\ServiceProvider;

/**
 * Amqp服务
 * Class AmqpService
 * @package Tang\Amqp
 */
class AmqpService extends ServiceProvider
{
    /**
     * @return \Tang\Amqp\Interfaces\IServers
     */
    public static function getService()
    {
        return parent::getService();
    }
    protected static function register()
    {
        $instance = static::initObject('amqp','\Tang\Amqp\Interfaces\IServers');
        $instance->setConfig(static::$config->get('amqp.*'));
        return $instance;
    }
}