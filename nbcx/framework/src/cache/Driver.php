<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\cache;

/**
 * Driver
 *
 * @package nb\cache
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
abstract class Driver {

    /**
     * 判断缓存是否存在
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    abstract public function has($name);

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    abstract public function get($name, $default = null);

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param int $expire 有效时间 0为永久
     * @return boolean
     */
    abstract public function set($name, $value, $expire);

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    abstract public function inc($name, $step = 1);

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    abstract public function dec($name, $step = 1);

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    abstract public function delete($name);

    /**
     * 清除缓存
     * @access public
     * @param string $pattern 匹配符
     * @return boolean
     */
    abstract public function rm($pattern = null);

    /**
     * 读取缓存并删除
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function pull($name) {
        $result = $this->get($name, false);
        if ($result) {
            $this->rm($name);
            return $result;
        }
        else {
            return;
        }
    }

    /**
     * 修改缓存里的某一些值
     * @param $name
     * @param array $value
     * @param null $expire
     * @return mixed
     */
    public function update($name, array $value, $expire = null) {
        $result = $this->get($name);
        if(is_array($result)) {
            return $this->set($name,array_merge($result,$value),$expire);
        }
        return $this->set($name,$value,$expire);
    }

}
