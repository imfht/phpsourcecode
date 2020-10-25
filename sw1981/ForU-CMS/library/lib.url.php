<?php
//获取访问者真实IP
function get_ip() {
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    //check ip FROM share internet
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    //to check ip is pass FROM proxy
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}
// 获取当前完整URL
function get_url() {
  $url_str = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  return 'http://' . $url_str;
}
function get_uri(){
  if (isset($_SERVER['REQUEST_URI'])) {
    $uri = $_SERVER['REQUEST_URI'];
  } else {
    if (isset($_SERVER['argv'])) {
      $uri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['argv'][0];
    } else {
      $uri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
    }
  }
  return $uri;
}
function http_get($url) {
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_TIMEOUT, 200);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
  curl_setopt($curl, CURLOPT_URL, $url);
  $output = curl_exec($curl);
  if (curl_errno($curl)) {
    echo 'Error: ' . curl_error($curl);
  }
  curl_close($curl);
  return $output;
}
function http_post($url, $data) {
  // 模拟提交数据函数
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  if (class_exists('\\CURLFile')) {
    curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
  } else {
    if (defined('CURLOPT_SAFE_UPLOAD')) {
      curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
    }
  }
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
  $output = curl_exec($curl);
  if (curl_errno($curl)) {
    echo 'Error: ' . curl_error($curl);
  }
  curl_close($curl);
  return $output;
}
function https_get($url) {
  // 模拟提交数据函数
  $curl = curl_init();
  // 启动一个CURL会话
  curl_setopt($curl, CURLOPT_URL, $url);
  // 要访问的地址
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  // 对认证证书来源的检查
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
  // 从证书中检查SSL加密算法是否存在
  curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
  // 模拟用户使用的浏览器
  @curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
  // 使用自动跳转
  curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
  // 自动设置Referer
  curl_setopt($curl, CURLOPT_TIMEOUT, 200);
  // 设置超时限制防止死循环
  curl_setopt($curl, CURLOPT_HEADER, 0);
  // 显示返回的Header区域内容
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  // 获取的信息以文件流的形式返回
  $output = curl_exec($curl);
  // 执行操作
  if (curl_errno($curl)) {
    echo 'Error: ' . curl_error($curl);
  }
  curl_close($curl);
  // 关闭CURL会话
  return $output;
}
function https_post($url, $data) {
  // 模拟提交数据函数
  $curl = curl_init();
  // 启动一个CURL会话
  curl_setopt($curl, CURLOPT_URL, $url);
  // 要访问的地址
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  // 对认证证书来源的检查
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
  // 从证书中检查SSL加密算法是否存在
  curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
  // 模拟用户使用的浏览器
  @curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
  // 使用自动跳转
  curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
  // 自动设置Referer
  curl_setopt($curl, CURLOPT_POST, 1);
  // 发送一个常规的Post请求
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  // Post提交的数据包
  curl_setopt($curl, CURLOPT_TIMEOUT, 200);
  // 设置超时限制防止死循环
  curl_setopt($curl, CURLOPT_HEADER, 0);
  // 显示返回的Header区域内容
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  // 获取的信息以文件流的形式返回
  $output = curl_exec($curl);
  // 执行操作
  if (curl_errno($curl)) {
    echo 'Error: ' . curl_error($curl);
  }
  curl_close($curl);
  // 关闭CURL会话
  return $output;
}
