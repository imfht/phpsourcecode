<?php 
namespace workerbase\func;

/**
 * 允许跨域请求
 * @param string $domain 允许访问的域名
 * @param string $method 允许访问的方法
 */
function allowOrigin($domain = '*', $method = 'GET, POST') {
    header("Access-Control-Allow-Origin:" . $domain);
    header('Access-Control-Allow-Methods:' . $method); //允许请求的方式
    header('Access-Control-Allow-Credentials:true');//是否支持cookie跨域
    header('Access-Control-Allow-Headers:x-requested-with, content-type');//允许x-requested-with，表明是AJax异步
}
