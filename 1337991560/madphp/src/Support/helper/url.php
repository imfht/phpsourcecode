<?php

/**
 * URL函数库
 * @author 徐亚坤 hdyakun@sina.com
 */

/**
 * 获取当前页面完整URL地址
 */
if (!function_exists('get_url')) {
    function get_url()
    {
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
            $sys_protocal = 'https://';
        } else {
            $sys_protocal = 'http://';
        }
        if ($_SERVER['PHP_SELF']) {
            $php_self = safe_replace($_SERVER['PHP_SELF']);
        } else {
            $php_self = safe_replace($_SERVER['SCRIPT_NAME']);
        }
        $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }
}

/**
 * 根据URL获取数组
 * @param  string
 * @return array
 */
if (!function_exists('get_url_arr')) {
    function get_url_arr($url)
    {
        $params = array();
        $query = parse_url($url, PHP_URL_QUERY);

        if ($query) {
            $queryParts = explode('&', $query);
        }
        if (isset($queryParts)) {
            foreach ($queryParts as $param) {
                $item = explode('=', $param);
                $params[$item[0]] = $item[1];
            }
        }
        return $params;
    }
}