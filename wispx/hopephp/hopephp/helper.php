<?php

// +----------------------------------------------------------------------
// | HopePHP
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.wispx.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: WispX <i@wispx.cn>
// +----------------------------------------------------------------------

// [ 助手函数 ]

use think\Db;
use hope\Debug;
use hope\Config;
use hope\Request;

if (!function_exists('dump')) {
    /**
     * 浏览器友好的变量输出
     * @param mixed     $var 变量
     * @param boolean   $echo 是否输出 默认为true 如果为false 则返回输出字符串
     * @param string    $label 标签 默认为空
     * @return void|string
     */
    function dump($var, $echo = true, $label = null)
    {
        return Debug::dump($var, $echo, $label);
    }
}

if (!function_exists('debug')) {
    /**
     * 记录时间（微秒）和内存使用情况
     * @param string            $start 开始标签
     * @param string            $end 结束标签
     * @param integer|string    $dec 小数位 如果是m 表示统计内存占用
     * @return mixed
     */
    function debug($start, $end = '', $dec = 6)
    {
        if ('' == $end) {
            Debug::remark($start);
        } else {
            return 'm' == $dec ? Debug::getRangeMem($start, $end) : Debug::getRangeTime($start, $end, $dec);
        }
    }
}

if (!function_exists('config')) {
    /**
     * 获取配置
     * @param string            $name 配置项，为空获取所有配置
     * @param string            $value 配置值，为空根据name获取配置，否则为设置配置
     * @return array|bool|string
     */
    function config($name = '', $value = '')
    {

        if(false !== strpos($name, '?')) {
            return Config::has(ltrim($name, '?'));
        }

        if(!empty($value)) {
            return Config::set($name, $value);
        }

        return Config::get($name);

    }
}

if (!function_exists('request')) {
    /**
     * 获取当前Request对象实例
     * @return Request
     */
    function request()
    {
        return Request::instance();
    }
}

if (!function_exists('db')) {
    /**
     * 实例化数据库类
     * @param string        $name 操作的数据表名称（不含前缀）
     * @param array|string  $config 数据库配置参数
     * @param bool          $force 是否强制重新连接
     * @return \think\db\Query
     */
    function db($name = '', $config = [], $force = false)
    {
        return Db::connect($config, $force)->name($name);
    }
}

if (!function_exists('exception')) {
    /**
     * 抛出异常处理
     *
     * @param string    $msg  异常消息
     * @param integer   $code 异常代码 默认为0
     * @param string    $exception 异常类
     *
     * @throws Exception
     */
    function exception($msg, $code = 0, $exception = '')
    {
        $e = $exception ?: '\Exception';
        throw new $e($msg, $code);
    }
}

if (!function_exists('input')) {
    /**
     * 获取请求数据，为空获取所有，使用方法：
     * input('s');
     * input('post.s')
     * @param string $name 参数名
     * @return array|mixed|null|string
     */
    function input($name = '')
    {
        $request = Request::instance();

        if (empty($name)) {
            return Request::instance()->param();
        } else {
            if (false !== strpos($name, '.')) {
                $data = explode('.', $name, 2);
                $data[0] = strtolower($data[0]);
                switch ($data[0]) {
                    case 'post':
                        return $request->post($data[1]);
                        break;
                    case 'get':
                        return $request->get($data[1]);
                        break;
                }
            }

            return $request->param($name);
        }
    }
}