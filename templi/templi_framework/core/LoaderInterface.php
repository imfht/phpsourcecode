<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14-6-29
 * Time: 下午9:31
 */

namespace framework\core;


interface LoaderInterface
{

    /**
     * 自动加载 类文件 包括 database、controller、libraries 类
     *
     * @param string $class
     * @throws Abnormal
     */
    public static function autoload($class);

    /**
     * 注册命名空间规则
     * @param string $nameSpace
     * @param string $path
     * @return bool
     */
    public function registerNameSpaceRule($nameSpace, $path);
}