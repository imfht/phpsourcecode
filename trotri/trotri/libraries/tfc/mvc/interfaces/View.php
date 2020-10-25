<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\mvc\interfaces;

/**
 * View interface file
 * 模板解析接口，用于分离业务层和展现层
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: View.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc.interfaces
 * @since 1.0
 */
interface View
{
    /**
     * 获取模板引擎
     * @return \tfc\mvc\interfaces\View
     */
    public function getEngine();

    /**
     * 魔术方法：通过模板变量名获取模板变量值
     * @param mixed $key
     * @return mixed
     */
    public function __get($key);

    /**
     * 魔术方法：设置模板变量
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value);

    /**
     * 魔术方法：判断模板变量是否已经存在
     * @param mixed $key
     * @return boolean
     */
    public function __isset($key);

    /**
     * 魔术方法：通过模板变量名删除模板变量值
     * @param mixed $key
     * @return boolean
     */
    public function __unset($key);

    /**
     * 设置一对或多对模板变量
     * @param mixed $key
     * @param mixed $value
     * @return \tfc\mvc\interfaces\View
     * @throws InvalidArgumentException 如果参数是对象，但是无法转换成数组，抛出异常
     */
    public function assign($key, $value = null);

    /**
     * 解析模板文件，根据需求输出到浏览器
     * @param string $tplName
     * @param boolean $display
     * @return string|void
     * @throws ErrorException 如果模板文件不存在，抛出异常
     */
    public function fetch($tplName, $display = false);

    /**
     * 将模板内容输出到浏览器
     * @param string $tplName
     * @return void
     */
    public function display($tplName);
}
