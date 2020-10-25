<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\ap;

/**
 * Bootstrap abstract class file
 * 程序引导类，在项目入口处执行，会依次执行类中以_init开头的方法，初始化项目参数
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Bootstrap.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
abstract class Bootstrap
{
    /**
     * @var array 寄存所有以_init开头的方法
     */
    protected $_classResources = null;

    /**
     * 运行此类，并依次执行类中以_init开头的方法
     * @return void
     */
    public function run()
    {
        $this->_executeResources();
    }

    /**
     * 依次执行本类中所有以_init开头的方法
     * @return void
     */
    protected function _executeResources()
    {
        foreach ($this->getClassResources() as $name => $method) {
            $this->$method();
        }
    }

    /**
     * 获取本类中所有以_init开头的方法
     * @return array
     */
    public function getClassResources()
    {
        if ($this->_classResources === null) {
            $this->_classResources = array();
            $methods = get_class_methods($this);
            if (is_array($methods)) {
                foreach ($methods as $method) {
                    if (strlen($method) > 5 && substr($method, 0, 5) === '_init') {
                        $name = strtolower(substr($method, 5));
                        $this->_classResources[$name] = $method;
                    }
                }
            }
        }

        return $this->_classResources;
    }

    /**
     * 获取本类中所有以_init开头的方法名，方法名中省略_init字符
     * @return array
     */
    public function getClassResourceNames()
    {
        return array_keys($this->getClassResources());
    }
}
