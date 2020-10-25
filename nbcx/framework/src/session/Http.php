<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\session;

use nb\Cookie;

/**
 * Swoole
 *
 * @package nb\session
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/29
 */
class Http extends Driver {

    /**
     * @var \SessionHandler
     */
    protected $hander;

    protected $sessionKey = 'PHPSESSID';

    protected $sessionID;

    protected $prefix = '';

    //当前session数据
    protected $data = [];

    protected $options = [
        'driver'=>'',
        'name'=>'',
        'path'=>'',
        'expire'             => 10,
        'var_session_id' => '',// SESSION_ID的提交变量,解决flash上传跨域
        'prefix'         => 'nb_',// SESSION 前缀
        'storage'        => 'native',// 驱动方式 支持redis memcache memcached
        'auto_start'     => true,// 是否自动开启 SESSION
    ];

    /**
     * 设置或者获取session作用域（前缀）
     * @param string $prefix
     * @return string|void
     */
    public function prefix($prefix = '') {
        if (empty($prefix) && null !== $prefix) {
            return $this->prefix;
        }
        else {
            $this->prefix = $prefix;
        }
    }

    /**
     * session初始化
     * @param array $config
     * @return void
     * @throws \Exception
     */
    public function __construct(array $options = []) {
        $options = $this->options = array_merge($this->options,$options);
        $class = false !== strpos($options['storage'], '\\') ? $options['storage'] : '\\nb\\session\\storage\\' . ucwords($options['storage']);
        $this->hander = new $class($options);
        $this->hander->open($options['path'], $options['name']);
        $this->sessionID = Cookie::get($this->sessionKey);
        if ($this->sessionID) {
            $data = $this->hander->read($this->sessionID);
            if($data) {
                $this->data = unserialize($data);
            }
        }
        else {
            $this->sessionID = self::string(8);
            Cookie::set($this->sessionKey,$this->sessionID,time() + $config['cache_expire']);
        }
    }

    static function string($length = 8, $number = true, $not_o0 = false) {
        $strings = 'ABCDEFGHIJKLOMNOPQRSTUVWXYZ';  //字符池
        $numbers = '0123456789';                    //数字池
        if ($not_o0) {
            $strings = str_replace('O', '', $strings);
            $numbers = str_replace('0', '', $numbers);
        }
        $pattern = $strings . $number;
        $max = strlen($pattern) - 1;
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern{mt_rand(0, $max)};    //生成php随机数
        }
        return $key;
    }

    /**
     * session设置
     * @param string $name session名称
     * @param mixed $value session值
     * @param string|null $prefix 作用域（前缀）
     * @return void
     */
    public function set($name, $value = '', $prefix = null) {
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;
        if (strpos($name, '.')) {
            // 二维数组赋值
            list($name1, $name2) = explode('.', $name);
            if ($prefix) {
                $this->data[$prefix][$name1][$name2] = $value;
            }
            else {
                $this->data[$name1][$name2] = $value;
            }
        }
        elseif ($prefix) {
            $this->data[$prefix][$name] = $value;
        }
        else {
            $this->data[$name] = $value;
        }
        $this->hander->write($this->sessionID,serialize($this->data));
    }

    /**
     * session获取
     * @param string $name session名称
     * @param string|null $prefix 作用域（前缀）
     * @return mixed
     */
    public function get($name = '', $prefix = null) {
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;
        $data = $this->data;
        if ('' == $name) {
            // 获取全部的session
            $value = $prefix ? (!empty($data[$prefix]) ? $data[$prefix] : []) : $data;
        }
        elseif ($prefix) {
            // 获取session
            if (strpos($name, '.')) {
                list($name1, $name2) = explode('.', $name);
                $value = isset($data[$prefix][$name1][$name2]) ? $data[$prefix][$name1][$name2] : null;
            }
            else {
                $value = isset($data[$prefix][$name]) ? $data[$prefix][$name] : null;
            }
        }
        else {
            if (strpos($name, '.')) {
                list($name1, $name2) = explode('.', $name);
                $value = isset($data[$name1][$name2]) ? $data[$name1][$name2] : null;
            }
            else {
                $value = isset($data[$name]) ? $data[$name] : null;
            }
        }
        return $value;
    }

    /**
     * session获取并删除
     * @param string $name session名称
     * @param string|null $prefix 作用域（前缀）
     * @return mixed
     */
    public function pull($name, $prefix = null) {
        $result = $this->get($name, $prefix);
        if ($result) {
            $this->delete($name, $prefix);
            return $result;
        }
        else {
            return;
        }
    }

    /**
     * session设置 下一次请求有效
     * @param string $name session名称
     * @param mixed $value session值
     * @param string|null $prefix 作用域（前缀）
     * @return void
     */
    public function flash($name, $value) {
        $this->set($name, $value);
        if (!$this->has('__flash__.__time__')) {
            $this->set('__flash__.__time__', $_SERVER['REQUEST_TIME_FLOAT']);
        }
        $this->push('__flash__', $name);
    }

    /**
     * 清空当前请求的session数据
     * @return void
     */
    public function flush() {
        $item = $this->get('__flash__');

        if (!empty($item)) {
            $time = $item['__time__'];
            if ($_SERVER['REQUEST_TIME_FLOAT'] > $time) {
                unset($item['__time__']);
                $this->delete($item);
                $this->set('__flash__', []);
            }
        }
    }

    /**
     * 删除session数据
     * @param string|array $name session名称
     * @param string|null $prefix 作用域（前缀）
     * @return void
     */
    public function delete($name, $prefix = null) {
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;
        if (is_array($name)) {
            foreach ($name as $key) {
                $this->delete($key, $prefix);
            }
        }
        elseif (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            if ($prefix) {
                unset($_SESSION[$prefix][$name1][$name2]);
            }
            else {
                unset($_SESSION[$name1][$name2]);
            }
        }
        else {
            if ($prefix) {
                unset($_SESSION[$prefix][$name]);
            }
            else {
                unset($_SESSION[$name]);
            }
        }
    }

    /**
     * 清空session数据
     * @param string|null $prefix 作用域（前缀）
     * @return void
     */
    public function clear($prefix = null) {
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;
        if ($prefix) {
            unset($_SESSION[$prefix]);
        }
        else {
            $_SESSION = [];
        }
    }

    /**
     * 判断session数据
     * @param string $name session名称
     * @param string|null $prefix
     * @return bool
     */
    public function has($name, $prefix = null) {
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;
        if (strpos($name, '.')) {
            // 支持数组
            list($name1, $name2) = explode('.', $name);
            return $prefix ? isset($_SESSION[$prefix][$name1][$name2]) : isset($_SESSION[$name1][$name2]);
        }
        else {
            return $prefix ? isset($_SESSION[$prefix][$name]) : isset($_SESSION[$name]);
        }
    }

    /**
     * 添加数据到一个session数组
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function push($key, $value) {
        $array = $this->get($key);
        if (is_null($array)) {
            $array = [];
        }
        $array[] = $value;
        $this->set($key, $array);
    }

    /**
     * 启动session
     * @return void
     */
    //public static function start() {
    //    session_start();
    //}

    /**
     * 销毁session
     * @return void
     */
    public function destroy() {
        if (!empty($_SESSION)) {
            $_SESSION = [];
        }
        session_unset();
        session_destroy();
    }

    /**
     * 重新生成session_id
     * @param bool $delete 是否删除关联会话文件
     * @return void
     */
    private static function regenerate($delete = false) {
        session_regenerate_id($delete);
    }

    /**
     * 暂停session
     * @return void
     */
    public static function pause() {
        // 暂停session
        session_write_close();
    }

}
