<?php

/**
 * 工具类
 * Date: 16-9-26
 * Time: 下午10:24
 * author :李华 yehong0000@163.com
 */
namespace tool;
class Tool
{
    /**
     * 生成一个25个到32个字符长度的订单号,默认生成25个
     *
     * @param $len
     */
    public static function getOrderId($len)
    {
        $len = $len ?: 25;
        $len = ($len > 32) ? 32 : $len;
        $fix = date('YmdHis');//前缀
        $mtLen = $len - 25;//随机数字串长度
        $fix = (string)$fix . (string)self::random($mtLen);
        $plus = 1E+10;
        $plus += M('Order')->add(array('id' => null));
        $fix .= (string)$plus;
        return $fix;
    }

    /**
     * 获得一个自增长id
     */
    public static function getUniqueNumber()
    {
        return M('Order')->add(array('id' => null));
    }

    /**
     * 生成指定长度的随机数字
     *
     * @param $length
     *
     * @return string
     */
    public static function random($length)
    {
        $chars = '0123456789';
        $hash = '';
        $max = 9;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

    /**
     * 获取指定长度的字符串
     *
     * @param $len
     */
    public static function randomStr($len)
    {
        $chars = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
            'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
            'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
            'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2',
            '3', '4', '5', '6', '7', '8', '9'
        );
        $charsLen = count($chars) - 1;
        shuffle($chars);    // 将数组打乱

        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }

    /**
     * 获取32位不重复的字符串
     */
    public static function getNonceStr()
    {
        return md5(uniqid(md5(microtime(true)), true));
    }

    /**
     * arr转ｘｍｌ
     *
     * @param $data
     * @param $rootNodeName
     * @param $xml
     *
     * @return mixed
     */
    public static function arrToXml($data, $rootNodeName = 'root', $xml = null)
    {
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }

        if ($xml == null) {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
        }

        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = 'num_' . $key;
            }

            // $key = preg_replace('/[0-9]+/i', '', $key);
            if (is_array($value)) {
                $node = $xml->addChild($key);
                self::arrToXml($value, $node);
            } else {
                $value = htmlentities($value);
                $xml->addChild($key, $value);
            }

        }
        return $xml->asXML();
    }

    /**
     * 获取开始时间，默认返回当天开始时间
     *
     * @param int $type 1天 | 2月 | 3年
     * @param string $dateTime
     *
     * @return bool|int
     */
    public static function getDayStartTime($type = 1, $dateTime = '')
    {
        $time = $dateTime ? strtotime($dateTime) : time();
        $time = $time ?: time();
        switch ($type) {
            case 1:
                return \mktime(0, 0, 0, date('m', $time), date('d', $time), date('y', $time));
                break;
            case 2:
                return \mktime(0, 0, 0, date('m', $time), 1, date('y', $time));
                break;
            case 3:
                return \mktime(0, 0, 0, 1, 1, date('y', $time));
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * 获取终点时间，默认返回当天结束时间
     *
     * @param int $type 1天 | 2月 | 3年
     * @param string $dateTime
     *
     * @return bool|int
     */
    public static function getDayEndTime($type = 1, $dateTime = '')
    {
        $time = $dateTime ? strtotime($dateTime) : time();
        $time = $time ?: time();
        switch ($type) {
            case 1:
                return \mktime(23, 59, 59, date('m', $time), date('d', $time), date('y', $time));
                break;
            case 2:
                return \mktime(23, 59, 59, date('m', $time), date('t'), date('y', $time));
                break;
            case 3:
                return \mktime(23, 59, 59, 12, 31, date('y', $time));
                break;
            default:
                return false;
                break;
        }
    }
}