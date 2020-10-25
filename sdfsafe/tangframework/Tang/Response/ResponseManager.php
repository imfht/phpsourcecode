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
namespace Tang\Response;
use Tang\Manager\Manager;

/**
 * Class ResponseManager
 * @package Tang\Response
 */
class ResponseManager extends Manager
{
    /**
     * 创建WebResponse
     * @return WebResponse
     */
    public function createWebDriver()
    {
        return new WebResponse($this->config['charset'],$this->config['contentType']);
    }

    /**
     * @see Manager::driver
     */
    public function driver($name = '')
    {
        return parent::driver($name);
    }
    /**
     * 创建CliResponse
     * @return CliResponse
     */
    public function createCliDriver()
    {
        return new CliResponse($this->config['charset']);
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
        return '\Tang\Response\IResponse';
    }
}