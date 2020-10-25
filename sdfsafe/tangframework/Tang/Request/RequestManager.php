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
namespace Tang\Request;
use Tang\Crypt\CryptService;
use Tang\Manager\Manager;
use Tang\Util\CSRF;
use Tang\Web\Cookie;
use Tang\Web\Session\SessionService;

/**
 * Requester管理器
 * Class RequestManager
 * @package Tang\Request
 */
class RequestManager extends Manager
{
    /**
     * 创建WebRequest
     * @return WebRequest
     */
    public function createWebDriver()
    {
        $CSRF = new CSRF(Cookie::getService(),CryptService::getService()->driver());
        return new WebRequest($CSRF);
    }

    /**
     * 创建CliRequest
     * @return CliRequest
     */
    public function createCliDriver()
    {
        return new CliRequest();
    }

    /**
     * 获取驱动
     * @param string $name
     * @return \Tang\Request\IRequest
     */
    public function driver($name = '')
    {
        return parent::driver($name);
    }

    /**
     * 获取默认的驱动名
     * @return mixed|string
     */
    protected function getDefaultDriver()
    {
        return 'Web';
    }
    protected function getIntreface()
    {
        return '\Tang\Request\IRequest';
    }
}