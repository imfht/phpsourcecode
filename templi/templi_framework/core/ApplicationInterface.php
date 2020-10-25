<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 15-3-21
 * Time: 上午10:39
 */

namespace framework\core;


interface ApplicationInterface
{
    /**
     * 执行 application
     */
    public function run();
    /**
     * 获取配置文件信息
     * $field 为空时 获取全部配置信息
     * $field 为字符串时 返回当前 索引 配置值
     * $field 为数组时 设置配置信息
     * @param string $field
     * @param mixed  $default
     * @return mixed
     */
    public function getConfig($field = NULL, $default = NULL);
} 