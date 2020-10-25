<?php
/**
 * @package     Helper.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月3日
 */

use SlimCustom\Libs\App;
use SlimCustom\Libs\Support\Arr;
use SlimCustom\Libs\Support\Collection;
use SlimCustom\Libs\Support\Str;
use SlimCustom\Libs\Download\Download;

include_once __DIR__ . '/Facades.php';

if (! function_exists('config')) {
    /**
     * 获取配置
     * 
     * @param string $key
     * @param mix $default
     * @return mixed|array|string|Closure
     */
    function config($key = null, $default = null)
    {
        if ($key) {
            list($filename, $key) = parseKey($key);
            if (! $configs = App::$instance->getContainer()['settings'][$filename]) {
                $framerConfigs = [];
                $filepath = App::$instance->framerPath() . 'config/' . $filename . '.php';
                if (is_file($filepath)) {
                    $framerConfigs = include_once $filepath;
                }
                $appConfigs = [];
                $filepath = App::$instance->configPath() . $filename . '.php';
                if (is_file($filepath)) {
                    $appConfigs = include_once $filepath;
                    ($appConfigs !== true) ?: $appConfigs = [];
                }
                App::$instance->getContainer()['settings'][$filename] = $configs = $framerConfigs + $appConfigs;
                if (! $configs && is_null($key)) {
                    return $default;
                }
            }
        }
        else {
            $configs = App::$instance->getContainer()['settings']->all();
        }
        return Arr::get($configs, $key, $default);
    }
}

if (! function_exists('arrayToXml')) {
    /**
     * 数组转Xml
     * 
     * @param array $arr
     * @param string $version
     * @param number $dom
     * @param number $item
     * @return string
     */
    function arrayToXml(array $arr, $version = '1.0', $dom = 0, $item = 0)
    {
        if (! $dom) {
            $dom = new \DOMDocument("1.0");
        }
        if (! $item) {
            $item = $dom->createElement("message");
            $dom->appendChild($item);
        }
        foreach ($arr as $key => $val) {
            $itemx = $dom->createElement(is_string($key) ? $key : "item");
            $item->appendChild($itemx);
            if (! is_array($val)) {
                $text = $dom->createTextNode($val);
                $itemx->appendChild($text);
            }
            else {
                arrayToXml($val, $version, $dom, $itemx);
            }
        }
        return $dom->saveXML();
    }
}

if (! function_exists('parseKey')) {
    /**
     * 解析key
     * @param string $key
     * @return mixed[]|unknown[]
     */
    function parseKey($key)
    {
        if ($key) {
            $keyArr = explode('.', $key);
            $firstKey = current($keyArr);
            unset($keyArr[0]);
            $childKey = implode('.', $keyArr) ?: null;;
            return [$firstKey, $childKey];
        }
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}


if (! function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed   $target
     * @param  string|array  $key
     * @param  mixed   $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        while (! is_null($segment = array_shift($key))) {
            if ($segment === '*') {
                if ($target instanceof Collection) {
                    $target = $target->all();
                } elseif (! is_array($target)) {
                    return value($default);
                }

                $result = Arr::pluck($target, $key);

                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }

            if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (! function_exists('data_set')) {
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @param  bool  $overwrite
     * @return mixed
     */
    function data_set(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if (! Arr::accessible($target)) {
                $target = [];
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    data_set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (Arr::accessible($target)) {
            if ($segments) {
                if (! Arr::exists($target, $segment)) {
                    $target[$segment] = [];
                }

                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || ! Arr::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (! isset($target->{$segment})) {
                    $target->{$segment} = [];
                }

                data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || ! isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];

            if ($segments) {
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }
}

if (! function_exists('countSpace')) {
    /**
     * Count space length
     * 
     * @param string $item
     * @param number $tplLength
     * @return string
     */
    function countSpace($item, $tplLength = 10)
    {
        $tpl = '';
        $spaceLength = $tplLength - strlen($item);
        $space = "";
        for ($i = 0; $i < $spaceLength; $i++) {
            $space .= " ";
        }
        return $space;
    }
}

if (! function_exists('studly_case')) {
    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    function studly_case($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }
}

if (! function_exists('array_get')) {
    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (! is_array($array) || ! array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (! function_exists('snake_case')) {
    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    function snake_case($value, $delimiter = '_')
    {
        return Str::snake($value, $delimiter);
    }
}

if (! function_exists('dumpRateInfo')) {
    /**
     * 打印进度条
     * 
     * @param unknown $rate
     */
    function dumpRateInfo($rate)
    {
        printf("progress: [%-50s] %d%% Done\r", str_repeat('#', $rate/100*50), $rate);
    }
}

if (! function_exists('download')) {
    /**
     * 文件下载
     * 
     * @param string $url
     * @param string $targetDir
     * @param boolean $displayProgress
     * @param integer $dirGenerationRule
     * @param \Closure $curlProgressCallback
     * @return \SlimCustom\Libs\Download\Download;
     */
    function download($url, $targetDir, $displayProgress = false, $dirGenerationRule = Download::DIR_GENERATION_BY_ARGS, $curlProgressCallback = null)
    {
        return new Download($url, $targetDir, $displayProgress, $dirGenerationRule, $curlProgressCallback);
    }
}

if (! function_exists('remoteFileSzie')) {
    /**
     * 获取远程文件大小
     *
     * @param string $url
     * @return mixed
     */
    function remoteFileSzie($url)
    {
        $headers = get_headers($url, true);
        return $headers['Content-Length'];
    }
}