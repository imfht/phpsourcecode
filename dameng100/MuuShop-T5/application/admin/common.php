<?php
/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */
function cloudU($url, $p = array())
{
    $url = adminU($url, $p);
    return str_replace(__ROOT__, '', $url);
}

function appstoreU($url, $p = array())
{
    return config('__CLOUD__') . cloudU($url, $p);
}

function formatLog($log)
{
    $log = explode("\r\n", $log);
    $log = '<li>' . implode('</li><li>', $log) . '</li>';
    return $log;
}

function show_cloud_cover($path){
    //不存在http://
    $not_http_remote=(strpos($path, 'http://') === false);
    //不存在https://
    $not_https_remote=(strpos($path, 'https://') === false);
    if ($not_http_remote && $not_https_remote) {
        //本地url
        return str_replace('//', '/', C('TMPL_PARSE_STRING.__CLOUD__') . $path); //防止双斜杠的出现
    } else {
        //远端url
        return $path;
    }
}

function guid(){
    
    mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);// "}"
    return $uuid;
    
}

function create_guid($namespace = '') {     
    static $guid = '';
    $uid = uniqid("", true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        $data .= $_SERVER['SERVER_ADDR'];
        $data .= $_SERVER['SERVER_PORT'];
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid =    
                substr($hash,  0,  8) . 
                '-' .
                substr($hash,  8,  4) .
                '-' .
                substr($hash, 12,  4) .
                '-' .
                substr($hash, 16,  4) .
                '-' .
                substr($hash, 20, 12);
            return $guid;
}