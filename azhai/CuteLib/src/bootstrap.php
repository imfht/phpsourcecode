<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace {

    // 要求PHP5.4以上版本
    if (version_compare(PHP_VERSION, '5.4.0') < 0) {
        die('PHP最低要求5.4版本');
    }

    defined('SRC_ROOT') or define('SRC_ROOT', __DIR__);
    defined('VENDOR_ROOT') or define('VENDOR_ROOT', dirname(SRC_ROOT) . '/vendor');
    defined('SQL_VERBOSE') or define('SQL_VERBOSE', true); //是否记录执行过的SQL语句
    defined('ERROR_LEVEL') or define('ERROR_LEVEL', E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    @error_reporting(ERROR_LEVEL);

    if (!class_exists('\\Cute\\Importer')) {
        require_once SRC_ROOT . '/Cute/Importer.php';
    }
    \Cute\Importer::getInstance();

    /**
     * 启动并缓存app实例
     */
    function app()
    {
        return \Cute\Application::$app;
    }

    /**
     * 开始的字符串相同
     *
     * @param string $haystack 可能包含子串的字符串
     * @param string $needle 要查找的子串
     * @return bool
     */
    function starts_with($haystack, $needle)
    {
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }


    /**
     * 结束的字符串相同
     *
     * @param string $haystack 可能包含子串的字符串
     * @param string $needle 要查找的子串
     * @return bool
     */
    function ends_with($haystack, $needle)
    {
        $ndlen = strlen($needle);
        return $ndlen === 0 || (strlen($haystack) >= $ndlen &&
            substr_compare($haystack, $needle, -$ndlen) === 0);
    }


    /**
     * 将内容转为另一种编码
     *
     * @param string $word 原始字符串
     * @param string $encoding 目标编码
     * @return string 转换后的字符串
     */
    function convert($word, $encoding = 'UTF-8')
    {
        $encoding = strtoupper($encoding);
        if (function_exists('mb_detect_encoding')) {
            return mb_detect_encoding($word, $encoding, true) ?
                $word : mb_convert_encoding($word, $encoding, 'UTF-8, GBK');
        } else if (function_exists('iconv')) {
            $from_encoding = $encoding === 'UTF-8' ? 'GBK' : 'UTF-8';
            return iconv($from_encoding, $encoding . '//IGNORE', $word);
        }
    }


    /**
     * base64解码
     */
    function b64decode($word)
    {
        if (preg_match('!([A-Za-z0-9+/= ]+)!', $word, $matches)) {
            $word = $matches[1];
        }
        return base64_decode($word);
    }


    /**
     * 调用函数/闭包/可invoke的对象
     * 不用call_user_func_array()，因为它有两个限制：
     * 一是性能较低，只有反射的一半多一点；
     * 二是$args中如果有引用参数，那么它们必须以引用方式传入。
     *
     * @param string /Closure/object $func 函数名/闭包/含__invoke方法的对象
     * @param array $args 参数数组，长度限制5个元素及以下
     * @return mixed 执行结果，没有找到可执行函数时返回null
     */
    function exec_function_array($func, array $args = [])
    {
        switch (count($args)) {
            case 0:
                return $func();
            case 1:
                return $func($args[0]);
            case 2:
                return $func($args[0], $args[1]);
            case 3:
                return $func($args[0], $args[1], $args[2]);
            case 4:
                return $func($args[0], $args[1], $args[2], $args[3]);
            case 5:
                return $func($args[0], $args[1], $args[2], $args[3], $args[4]);
            default:
                if (is_object($func)) {
                    $ref = new ReflectionMethod($func, '__invoke');
                    return $ref->invokeArgs($func, $args);
                } else if (is_callable($func)) {
                    $ref = new ReflectionFunction($func);
                    return $ref->invokeArgs($args);
                }
        }
    }


    /**
     * 调用类/对象方法
     *
     * @param object /class $clsobj 对象/类
     * @param string $method 方法名
     * @param array $args 参数数组，长度限制5个元素及以下
     * @return mixed 执行结果，没有找到可执行方法时返回null
     */
    function exec_method_array($clsobj, $method, array $args = [])
    {
        if (is_object($clsobj)) {
            switch (count($args)) {
                case 0:
                    return $clsobj->{$method}();
                case 1:
                    return $clsobj->{$method}($args[0]);
                case 2:
                    return $clsobj->{$method}($args[0], $args[1]);
                case 3:
                    return $clsobj->{$method}($args[0], $args[1], $args[2]);
                case 4:
                    return $clsobj->{$method}($args[0], $args[1], $args[2], $args[3]);
                case 5:
                    return $clsobj->{$method}($args[0], $args[1], $args[2], $args[3], $args[4]);
            }
        }
        if (method_exists($clsobj, $method)) {
            $ref = new ReflectionMethod($clsobj, $method);
            if ($ref->isPublic() && !$ref->isAbstract()) {
                if ($ref->isStatic()) {
                    return $ref->invokeArgs(null, $args);
                } else {
                    return $ref->invokeArgs($clsobj, $args);
                }
            }
        }
    }


    /**
     * 创建对象
     *
     * @param string $class 类名
     * @param array $args 参数数组
     * @return mixed 执行结果，没有找到类时返回null
     */
    function exec_construct_array($class, array $args = [])
    {
        switch (count($args)) {
            case 0:
                return new $class();
            case 1:
                return new $class($args[0]);
            case 2:
                return new $class($args[0], $args[1]);
            case 3:
                return new $class($args[0], $args[1], $args[2]);
            case 4:
                return new $class($args[0], $args[1], $args[2], $args[3]);
            case 5:
                return new $class($args[0], $args[1], $args[2], $args[3], $args[4]);
            default:
                if (class_exists($class)) {
                    $ref = new ReflectionClass($class);
                    return $ref->newInstanceArgs($args);
                }
        }
    }

}
