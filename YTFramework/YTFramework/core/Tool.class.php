<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: Tool.class.php 89 2016-04-21 02:53:46Z lixiaomin $
 *  @created    2015-10-10
 *  工具类
 * =============================================================================                   
 */

namespace core;

class Tool
{

    /**
     * 随机数
     * @param $len    随机数长度
     */
    static function roundStr($len = '4')
    {
        $array_str = '';
        $round_num = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $round_num = str_split($round_num);
        $i = 0;
        for ($i; $i < $len; $i++) {
            $array_str .= $round_num[mt_rand(0, 31)];
        }
        unset($round_num);
        return $array_str;
    }

    /**
     * 随机数
     * @param $len    随机数长度
     */
    static function roundInt($len = '6')
    {
        $array_str = '';
        $round_num = '12345678912345678912345678912345';
        $round_num = str_split($round_num);
        $i = 0;
        for ($i; $i < $len; $i++) {
            $array_str .= $round_num[mt_rand(0, 31)];
        }
        unset($round_num);
        return $array_str;
    }

    /**
     * 检查邮箱
     * @param str $email 邮箱
     * @return bool
     */
    static function checkEmail($email = '')
    {

        return (preg_match('/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/', $email)) ? true : false;
    }

    /**
     * 检查手机号
     * @param number $mobile 手机号码
     */
    static function checkMobile($mobile)
    {
        return preg_match("/^(13|15|18|14|17)+[0-9]{9}$/", $mobile) ? true : false;
    }

    /**
     * 16位数字定单号生成
     */
    static function createOrderNumber()
    {
        return date('YmdHis') . substr(microtime(), 2, 4);
    }

    /**
     * 计算某个经纬度的周围某段距离的正方形的四个点
     *
     * @param lng float 经度
     * @param lat float 纬度
     * @param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
     * @return array 正方形的四个点的经纬度坐标
     */
    static function getGPSPoint($lng, $lat, $distance = 0.5)
    {
        $earth_radius = 6371;
        $dlng = 2 * asin(sin($distance / (2 * $earth_radius)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);

        $dlat = $distance / $earth_radius;
        $dlat = rad2deg($dlat);

        return array(
            'left-top' => array('lat' => $lat + $dlat, 'lng' => $lng - $dlng),
            'right-top' => array('lat' => $lat + $dlat, 'lng' => $lng + $dlng),
            'left-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng - $dlng),
            'right-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng + $dlng)
        );
    }

    /**
     *  @desc 根据两点间的经纬度计算距离
     *  @param float $lat 纬度值
     *  @param float $lng 经度值
     */
    static function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000;


        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;

        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }

    /**
     * remove xss 
     * @param str $val 需要过滤的内容
     * @return str 已经过滤的内容
     */
    static function remove_xss($val)
    {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed  
        // this prevents some character re-spacing such as <java\0script>  
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs  
        $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

        // straight replacements, the user should never need these since they're normal characters  
        // this prevents like <IMG SRC=@avascript:alert('XSS')>  
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional 
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars 
            // @ @ search for the hex values 
            $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ; 
            // @ @ 0{0,7} matches '0' zero to seven times  
            $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ; 
        }

        // now the only remaining whitespace attacks are \t, \n, and \r 
        $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $found = true; // keep replacing as long as the previous round replaced something 
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|';
                        $pattern .= '|(&#0{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag  
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags  
                if ($val_before == $val) {
                    // no replacements were made, so exit the loop  
                    $found = false;
                }
            }
        }
        return $val;
    }

    /**
     * 验证码 (直接输出png格式图片资源)
     *
     * @param  str $session_name session名字
     * @param  int $num 验证码数量
     * @param  int $w 验证码长度
     * @param  int $h 验证码高度
     * 
     */
    static function yzm($session_name = 'yzm', $width = 100, $height = 30)
    {


        header("Content-type: image/png");
        /* 创建图片设置字体颜色 */
        $im = imagecreate($width, $height);
        $red = imagecolorallocate($im, 255, 255, 255);
        $white = imagecolorallocate($im, 255, 255, 255);
        /* 随机生成两个数字 */
        $num1 = rand(1, 20);
        $num2 = rand(1, 20);
        /* 设置图片背景颜色 */
        $gray = imagecolorallocate($im, 118, 151, 199);
        $black = imagecolorallocate($im, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
        /* 创建图片背景 */
        imagefilledrectangle($im, 0, 0, $width, $height, $black);
        /* 在画布上随机生成大量点 */
        for ($i = 0; $i < 80; $i++) {
            imagesetpixel($im, rand(0, $width), rand(0, $height), $gray);
        }
        /* 将计算验证码写入到图片中 */
        imagestring($im, 5, 5, 4, $num1, $red);
        imagestring($im, 5, 30, 3, "+", $red);
        imagestring($im, 5, 45, 4, $num2, $red);
        imagestring($im, 5, 70, 3, "=", $red);
        imagestring($im, 5, 80, 2, "?", $white);
        //写入session验证码
        $_SESSION[$session_name] = $num1 + $num2;
        imagepng($im);
        imagedestroy($im);
    }

    /**
     * 获取用户ip
     * @return str ip地址
     */
    static function getip()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], 'unknown')) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $temp = [];
        preg_match("/[\d\.]{7,15}/", $ip, $temp);
        $ip = $temp[0] ? $temp[0] : 'unknown';
        unset($temp);
        return $ip;
    }

    /**
     * 检查身份证号码
     * @param string $carid 身份证号码
     */
    static function checkIdCard($id)
    {
        return preg_match(" /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/", $id) ? true : false;
    }

    /**
     * 检查QQ
     * @param number $qq 身份证号码
     */
    static function checkQq($qq)
    {
        return preg_match('/^[1-9][0-9]{5,15}$/', $qq) ? true : false;
    }

    /**
     * 替换url_query
     * @param array $url
     * @param type $k
     * @param type $v
     * @return str 返回拼接好的url query
     */
    static function urlReplace($url = [], $k = '', $v = '')
    {
        $url[$k] = $v;
        return http_build_query($url);
    }

    /**
     * 检查用户名格式
     * @param type $username 用户名
     * @return boolean
     */
    static function checkUsername($username)
    {
        $strlen = strlen($username);
        if (!preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username)) {
            return false;
        } elseif (20 < $strlen || $strlen < 4) {
            return false;
        }
        return true;
    }

    /**
     * 银行卡号码正则
     * @param type  number
     * @return boolean 
     */
    static function bankcarid($carid)
    {
        return preg_match('/^[1-9][0-9]{18}$/', $carid) ? true : false;
    }

    /**
     * 多个连续空格只保留一个     
     * @param string $string 待转换的字符串
     * @return string $string 转换后的字符串
     */
    static function merge_spaces($string)
    {
        return preg_replace("/\s(?=\s)/", "\\1", $string);
    }

    /**
     * 字符串截取
     * @param $string
     * @param $length
     * @param string $dot
     * @return string
     */
    static function cutstr($string, $length, $dot = ' ...')
    {
        if (strlen($string) <= $length) {
            return $string;
        }
        $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
        $strcut = '';
        if (strtolower('UTF-8') == 'utf-8') {
            $n = $tn = $noc = 0;
            while ($n < strlen($string)) {
                $t = ord($string[$n]);
                if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $tn = 1;
                    $n++;
                    $noc++;
                } elseif (194 <= $t && $t <= 223) {
                    $tn = 2;
                    $n += 2;
                    $noc += 2;
                } elseif (224 <= $t && $t <= 239) {
                    $tn = 3;
                    $n += 3;
                    $noc += 2;
                } elseif (240 <= $t && $t <= 247) {
                    $tn = 4;
                    $n += 4;
                    $noc += 2;
                } elseif (248 <= $t && $t <= 251) {
                    $tn = 5;
                    $n += 5;
                    $noc += 2;
                } elseif ($t == 252 || $t == 253) {
                    $tn = 6;
                    $n += 6;
                    $noc += 2;
                } else {
                    $n++;
                }
                if ($noc >= $length) {
                    break;
                }
            }
            if ($noc > $length) {
                $n -= $tn;
            }
            $strcut = substr($string, 0, $n);
        } else {
            for ($i = 0; $i < $length; $i++) {
                $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
            }
        }
        $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
        return $strcut . $dot;
    }

    /**
     * 检测是否是手机访问
     * @return boolean
     */
    static function isMobile()
    {
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
        $mobile_browser = '0';
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
            $mobile_browser++;
        if ((isset($_SERVER['HTTP_ACCEPT'])) and ( strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
            $mobile_browser++;
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
            $mobile_browser++;
        if (isset($_SERVER['HTTP_PROFILE']))
            $mobile_browser++;
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
            'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
            'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
            'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
            'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
            'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
            'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
            'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
            'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
        );
        if (in_array($mobile_ua, $mobile_agents))
            $mobile_browser++;
        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
            $mobile_browser++;
        // Pre-final check to reset everything if the user is on Windows  
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
            $mobile_browser = 0;
        // But WP7 is also Windows, with a slightly different characteristic  
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
            $mobile_browser++;
        if ($mobile_browser > 0)
            return true;
        else
            return false;
    }

    /**
     * 判断SSL是否启用
     * @return boolean
     */
    static function isSSL()
    {
        if (!isset($_SERVER['HTTPS']))
            return false;
        if ($_SERVER['HTTPS'] === 1) {  //Apache  
            return true;
        } elseif ($_SERVER['HTTPS'] === 'on') { //IIS  
            return true;
        } elseif ($_SERVER['SERVER_PORT'] == 443) { //其他  
            return true;
        }
        return false;
    }

}
