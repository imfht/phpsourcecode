<?php
header('Content-Type:image/svg+xml');
$urlForSvg = '';   
function randColor() 
{ 
  $str='3456789ABC'; 
    $estr='#'; 
    $len=strlen($str); 
    for($i=1;$i<=6;$i++) 
    { 
        $num=rand(0,$len-1);   
        $estr=$estr.$str[$num];  
    } 
    return $estr; 
} 
/**
 * CURL GET
 *
 * @param string 请求地址
 * @param array 请求头
 * @param string COOKIES
 * @param boolean 是否返回header
 * @param boolean 是否后台请求
 * @param integer 超时时间
 * @param array 使用代理
 * @return mixed 
 */
function httpGetFull($url, $header = [], $cookies = "", $returnHeader = false, $isBackGround = false, $timeout = 0, $proxy = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    if ($timeout) {
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    }
    if (!empty($proxy)) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy['ip']);
        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, "taras:taras-ss5");
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $isBackGround ? 0 : 1);
    curl_setopt($ch, CURLOPT_HEADER, $returnHeader ? 1 : 0);
    $output = curl_exec($ch);
    if ($timeout) {
        if ($output === FALSE) {
            if (in_array(curl_errno($ch), [28])) {
                $output = 'TIMEOUT';
            } else {
                $output = 'ERROR';
            }
        }
    }
    curl_close($ch);
    return $output;
}

function get_client_ip()
{
    foreach (array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ) as $key) {
        if (array_key_exists($key, $_SERVER)) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if ((bool) filter_var(
                    $ip,
                    FILTER_VALIDATE_IP,
                    FILTER_FLAG_IPV4
                    // FILTER_FLAG_NO_PRIV_RANGE |
                    // FILTER_FLAG_NO_RES_RANGE
                )) {
                    return $ip;
                }
            }
        }
    }
    return null;
}

/**
 * 获取操作系统
 *
 * @return string
 */
function  getOs()
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return 'Other';
    }
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($agent, 'windows nt')) {
        $platform = 'Windows';
    } elseif (strpos($agent, 'macintosh')) {
        $platform = 'MacOS';
    } elseif (strpos($agent, 'ipod')) {
        $platform = 'iPod';
    } elseif (strpos($agent, 'ipad')) {
        $platform = 'iPad';
    } elseif (strpos($agent, 'iphone')) {
        $platform = 'iPhone';
    } elseif (strpos($agent, 'android')) {
        $platform = 'Android';
    } elseif (strpos($agent, 'unix')) {
        $platform = 'Unix';
    } elseif (strpos($agent, 'linux')) {
        $platform = 'Linux';
    } else {
        $platform = 'Other';
    }
    return $platform;
}
/**
 * 获取浏览器
 *
 * @return void
 */
function  getBrowser()
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return 'Unknown';
    }
    $agent = $_SERVER["HTTP_USER_AGENT"];
    if (strpos($agent, 'MSIE') !== false || strpos($agent, 'rv:11.0')) //ie11判断
    {
        return "IE";
    } else if (strpos($agent, 'Firefox') !== false) {
        return "Firefox";
    } else if (strpos($agent, 'Chrome') !== false) {
        return "Chrome";
    } else if (strpos($agent, 'Opera') !== false) {
        return 'Opera';
    } else if ((strpos($agent, 'Chrome') == false) && strpos($agent, 'Safari') !== false) {
        return 'Safari';
    } else {
        return 'Unknown';
    }
}
?>