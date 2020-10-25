<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\cookie;

/**
 * Driver
 *
 * @package nb\cookie
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
abstract class Driver {

    /**
     * 设置或者获取cookie作用域（前缀）
     * @param string $prefix
     * @return string|void
     */
    abstract public function prefix($prefix = '');

    /**
     * Cookie 设置、获取、删除
     *
     * @param string $name cookie名称
     * @param mixed $value cookie值
     * @param mixed $option 可选参数 可能会是 null|integer|string
     *
     * @return mixed
     */
    abstract public function set($name, $value = '', $option = null);

    /**
     * 永久保存Cookie数据
     * @param string $name cookie名称
     * @param mixed $value cookie值
     * @param mixed $option 可选参数 可能会是 null|integer|string
     * @return void
     */
    abstract public function forever($name, $value = '', $option = null);

    /**
     * 判断Cookie数据
     * @param string $name cookie名称
     * @param string|null $prefix cookie前缀
     * @return bool
     */
    abstract public function has($name, $prefix = null);

    /**
     * Cookie获取
     * @param string $name cookie名称
     * @param string|null $prefix cookie前缀
     * @return mixed
     */
    abstract public function get($name, $prefix = null);

    /**
     * Cookie删除
     * @param string $name cookie名称
     * @param string|null $prefix cookie前缀
     * @return mixed
     */
    abstract public function delete($name, $prefix = null);

    /**
     * Cookie清空
     * @param string|null $prefix cookie前缀
     * @return mixed
     */
    abstract public function clear($prefix = '');

}
