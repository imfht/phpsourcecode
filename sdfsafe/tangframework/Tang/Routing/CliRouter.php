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
/**
 * Cli模式下的路由
 * Class CliRouter
 * @package Tang\Routing
 */
class CliRouter extends BaseRouter implements IRouter
{
    protected $config = array();
    public function router()
    {
        $this->moduleValue = ucfirst($this->request->get($this->config['moduleName'],$this->config['defaultModule']));
        $this->controllerValue = ucfirst($this->request->get($this->config['controllerName'],$this->config['defaultController']));
        $this->actionValue = $this->request->get($this->config['actionName'],$this->config['defaultAction']);
    }
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * 获取默认的配置
     * @return array
     */
    public function getDefaultConfig()
    {
        return [
            'moduleName' => 'm',
            'controllerName'=>'c',
            'actionName' => 'a',
            'defaultModule' => 'index',
            'defaultController' => 'index',
            'defaultAction' => 'main'
        ];
    }

    public function getType()
    {
        return 'Cli';
    }
}