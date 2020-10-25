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
namespace Tang\Routing;
use Tang\Manager\Manager;

/**
 * 路由管理器
 * Class RouterManager
 * @package Tang\Routing
 */
class RouterManager extends Manager
{
    /**
     * 创建WEB路由
     * @return WebRouter
     */
    public function createWebDriver()
    {
        return new WebRouter();
    }

    /**
     * 创建CLI路由
     * @return CliRouter
     */
    public function createCliDriver()
    {
        return new CliRouter();
    }

    /**
     * @see Manager::driver
     * @param string $name
     * @return \Tang\Routing\IRouter
     */
    public function driver($name='')
    {
        return parent::driver($name);
    }

    /**
     * @see Manager::getDefaultDriver
     */
    protected function getDefaultDriver()
    {
        return 'Web';
    }

    /**
     * @see Manager::getIntreface
     */
    protected function getIntreface()
    {
        return '\Tang\Routing\IRouter';
    }
}