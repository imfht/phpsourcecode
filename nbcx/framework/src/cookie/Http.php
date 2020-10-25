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

use nb\Config;
use nb\Pool;
use nb\Request;

/**
 * Swoole
 *
 * @package nb\cookie
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/28
 */
class Http extends Driver {

    public $config = [
        'driver'=>'',
        'prefix'    => '',// cookie 名称前缀
        'expire'    => 0,// cookie 保存时间
        'path'      => '/',// cookie 保存路径
        'domain'    => '',// cookie 有效域名
        'secure'    => false,//  cookie 启用安全传输
        'httponly'  => '',// httponly设置
        'setcookie' => true,// 是否使用 setcookie
    ];

    protected $request;

    protected $response;

    /**
     * Cookie初始化
     * @param array $config
     * @return void
     */
    public function __construct(array $config = []) {
        $config and $this->config = array_merge($this->config,$config);

        if (!empty($this->config['httponly'])) {
            ini_set('session.cookie_httponly', 1);
        }
        $this->request  = Pool::value('\swoole\http\Request');
        $this->response = Pool::value('\swoole\http\Response');
    }

    /**
     * 设置或者获取cookie作用域（前缀）
     * @param string $prefix
     * @return string|void
     */
    public function prefix($prefix = '') {
        if (empty($prefix)) {
            return $this->config['prefix'];
        }
        $this->config['prefix'] = $prefix;
    }

    /**
     * Cookie 设置、获取、删除
     *
     * @param string $name cookie名称
     * @param mixed $value cookie值
     * @param mixed $option 可选参数 可能会是 null|integer|string
     *
     * @return mixed
     */
    public function set($name, $value = '', $option = null) {
        // 参数设置(会覆盖黙认设置)
        if (!is_null($option)) {
            if (is_numeric($option)) {
                $option = ['expire' => $option];
            }
            elseif (is_string($option)) {
                parse_str($option, $option);
            }
            $config = array_merge($this->config, array_change_key_case($option));
        }
        else {
            $config = $this->config;
        }
        $name = $config['prefix'] . $name;
        // 设置cookie
        if (is_array($value)) {
            array_walk_recursive($value, 'self::jsonFormatProtect', 'encode');
            $value = 'nb:' . json_encode($value);
        }
        $expire = !empty($config['expire']) ? Request::ins()->requestTime + intval($config['expire']) : 0;
        $this->response->cookie($name, $value, $expire, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
        $this->request->cookie[$name] = $value;
    }

    /**
     * 永久保存Cookie数据
     * @param string $name cookie名称
     * @param mixed $value cookie值
     * @param mixed $option 可选参数 可能会是 null|integer|string
     * @return void
     */
    public function forever($name, $value = '', $option = null) {
        if (is_null($option) || is_numeric($option)) {
            $option = [];
        }
        $option['expire'] = 315360000;
        $this->set($name, $value, $option);
    }

    /**
     * 判断Cookie数据
     * @param string $name cookie名称
     * @param string|null $prefix cookie前缀
     * @return bool
     */
    public function has($name, $prefix = null) {
        $prefix = !is_null($prefix) ? $prefix : $this->config['prefix'];
        $name = $prefix . $name;
        return isset($this->response->cookie[$name]);
    }

    /**
     * Cookie获取
     * @param string $name cookie名称
     * @param string|null $prefix cookie前缀
     * @return mixed
     */
    public function get($name, $prefix = null) {
        $prefix = !is_null($prefix) ? $prefix : $this->config['prefix'];
        $name = $prefix . $name;
        if (isset($this->request->cookie[$name])) {
            $value = $this->request->cookie[$name];
            if (0 === strpos($value, 'nb:')) {
                $value = substr($value, 6);
                $value = json_decode($value, true);
                array_walk_recursive($value, 'self::jsonFormatProtect', 'decode');
            }
            return $value;
        }
        return null;
    }

    /**
     * Cookie删除
     * @param string $name cookie名称
     * @param string|null $prefix cookie前缀
     * @return mixed
     */
    public function delete($name, $prefix = null) {
        $config = $this->config;
        $prefix = is_null($prefix) ? $config['prefix'] : $prefix;
        $name = $prefix . $name;
        if ($config['setcookie']) {
            $this->response->cookie(
                $name,
                '',
                Http::obj()->requestTime - 3600,
                $config['path'],
                $config['domain'],
                $config['secure'],
                $config['httponly']
            );
        }
        // 删除指定cookie
        unset($this->response->cookie[$name]);
    }

    /**
     * Cookie清空
     * @param string|null $prefix cookie前缀
     * @return mixed
     */
    public function clear($prefix = '') {
        // 清除指定前缀的所有cookie
        if (empty($this->request->cookie)) {
            return;
        }
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $config = $this->config;
        $prefix = is_null($prefix) ? $config['prefix'] :  $prefix;
        // 如果前缀为空字符串将不作处理直接返回
        foreach ($_COOKIE as $key => $val) {
            $key = $prefix.$key;
            if ($config['setcookie']) {
                $this->response->cookie(
                    $key,
                    '',
                    Request::driver()->requestTime - 3600,
                    $config['path'],
                    $config['domain'],
                    $config['secure'],
                    $config['httponly']
                );
            }
            unset($this->request->cookie[$key]);
        }
    }

}