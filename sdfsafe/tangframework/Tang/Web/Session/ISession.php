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
namespace Tang\Web\Session;
use Tang\Manager\IManager;

interface ISession extends IManager
{
    /**
     * 获取session值
     * @param string $name session名称
     * @param string $defaultValue  如果没有值返回的一个默认值
     * @return mixed
     */
    public function get($name,$defaultValue='');

    /**
     * 设置session
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function set($name,$value);

    /**
     * 删除session
     * @param string $name
     * @return mixed
     */
    public function delete($name);

    /**
     * 销毁session会话
     * @return mixed
     */
    public function destroy();
}