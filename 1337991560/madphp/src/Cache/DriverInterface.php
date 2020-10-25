<?php

namespace Madphp\Cache;

interface DriverInterface
{

    function __construct($config = array());

    /**
     * 检测缓存驱动是否可用
     * @return mixed
     */
    function checkDriver();

    /**
     * 设置缓存
     * @param $keyword
     * @param string $value
     * @param int $time
     * @param array $option
     * @return mixed
     */
    function driverSet($keyword, $value = "", $time = 300, $option = array());

    /**
     * 获取缓存
     * @param $keyword
     * @param array $option
     * @return mixed
     */
    function driverGet($keyword, $option = array());

    /**
     * 缓存信息
     * @param array $option
     * @return mixed
     */
    function driverStats($option = array());

    /**
     * 删除缓存
     * @param $keyword
     * @param array $option
     * @return mixed
     */
    function driverDelete($keyword, $option = array());

    /**
     * 清空缓存
     * @param array $option
     * @return mixed
     */
    function driverClean($option = array());

    /**
     * 驱动是否存在
     * @param $keyword
     * @return mixed
     */
    function driverIsExisting($keyword);
}