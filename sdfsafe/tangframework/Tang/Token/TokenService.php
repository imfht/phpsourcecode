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
namespace Tang\Token;
use Tang\Crypt\CryptService;
use Tang\Services\ServiceProvider;
use Tang\Web\Session\SessionService;

/**
 * 令牌实现
 * Class TokenService
 * @package Tang\Token
 */
class TokenService extends ServiceProvider
{
    /**
     * @return \Tang\Token\IToken
     */
    public static function getService()
    {
        return parent::getService();
    }
    protected static function register()
    {
        $config = static::$config->replaceGet('token',array('cryptDriver'=>'','expire'=>0));
        $instance = static::initObject('token','\Tang\Token\IToken');
        $instance->setConfig($config);
        $instance->setCrypt(CryptService::getService()->driver($config['cryptDriver']));
        $instance->setSession(SessionService::getService());
        return $instance;
    }
}