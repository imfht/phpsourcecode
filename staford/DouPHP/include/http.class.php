<?php

/**
 * http类
 * @abstract 提供网页抓取、Header头信息控制、来源伪造等功能
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Http
{
    //浏览器UA信息
    private static $agant = array('pc' =>
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 UBrowser/2.0.754.0 Safari/537.36',
            'mobile' => 'User-Agent: Opera/9.80 (Android 2.3.4; Linux; Opera Mobi/build-1107180945; U; en-GB) Presto/2.8.149 Version/11.10');

    /**
     * @abstract 使用cURL抓取页面
     */
    public static function cUrl($url, $autoCookie = false)
    {
        if (!function_exists("curl_init")) {
            die('Your server does not support "curl"');
        }
        $cjk = tempnam('/tmp', 'cookie');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); //超时60S
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cjk); //保存cookie
        if ($autoCookie) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cjk); //读取cookie
        }
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, self::$agant['pc']);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    public static function cPost($url, $field = '', $refer = 'http://www.baidu.com')
    {
        if (!function_exists("curl_init")) {
            die('Your server does not support "curl"');
        }
        $cjk = tempnam('/tmp', 'cookie');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $refer);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field);
        curl_setopt($ch, CURLOPT_USERAGENT, self::$agant['pc']);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    /**
     * @abstract 使用F_G_C()函数抓取页面
     */
    public static function gUrl($url)
    {
        if (!function_exists('file_get_contents')) {
            die('Your server does not support "file_get_contents"');
        }
        if ($res = file_get_contents($url)) {
            return $res;
        } else {
            return false;
        }
    }

    /**
     * @abstract 编码转换
     */
    public static function getEncoding($string, $outEncoding = 'UTF-8')
    {
        $encoding = "UTF-8";
        for ($i = 0; $i < strlen($string); $i++) {
            if (ord($string{$i}) < 128)
                continue;

            if ((ord($string{$i}) & 224) == 224) {
                //第一个字节判断通过
                $char = $string{++$i};
                if ((ord($char) & 128) == 128) {
                    //第二个字节判断通过
                    $char = $string{++$i};
                    if ((ord($char) & 128) == 128) {
                        $encoding = "UTF-8";
                        break;
                    }
                }
            }
            if ((ord($string{$i}) & 192) == 192) {
                //第一个字节判断通过
                $char = $string{++$i};
                if ((ord($char) & 128) == 128) {
                    // 第二个字节判断通过
                    $encoding = "GB2312";
                    break;
                }
            }
        }
        if (strtoupper($encoding) == strtoupper($outEncoding))
            return $string;
        else
            return iconv($encoding, $outEncoding, $string);
    }

}

?>