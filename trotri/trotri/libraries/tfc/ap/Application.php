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
 * Application abstract class file
 * 框架所有应用类的基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Application.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
abstract class Application
{
    /**
     * 魔术方法：请求get开头的方法，获取一个受保护的属性值
     * @param string $name
     * @return mixed
     * @throws ErrorException 如果该属性名的getter方法不存在，抛出异常
     */
    public function __get($name)
    {
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        else {
            throw new ErrorException(sprintf(
                'Property "%s.%s" was not defined.', get_class($this), $method
            ));
        }
    }

    /**
     * 魔术方法：请求set开头的方法，设置一个受保护的属性值
     * @param string $name
     * @param mixed $value
     * @return void
     * @throws ErrorException 如果该属性名的setter方法不存在，抛出异常
     */
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
        else {
            throw new ErrorException(sprintf(
                'Property "%s.%s" was not defined.', get_class($this), $method
            ));
        }
    }
}
