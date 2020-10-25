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
namespace Tang\Manager;
use Tang\Interfaces\ISetConfig;

/**
 * 管理器接口
 * Interface IManager
 * @package Tang\Manager
 */
interface IManager extends ISetConfig
{
    /**
     * 添加$name驱动
     * $driver为一个实例化的对象，必须实现驱动接口
     * @param $name
     * @param $driver
     * @return mixed
     */
    public function addDriver($name,$driver);

    /**
     * 获取接口 $name 为空的话则获取默认的驱动
     * @param string $name
     * @return mixed
     */
    public function driver($name = '');
}