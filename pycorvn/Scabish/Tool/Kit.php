<?php
namespace Scabish\Tool;

/**
 * Scabish\Tool\Kit
 * 常用函数集
 * 
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2016-12-7
 */
class Kit {
    
    /**
     * 数组转标准对象
     * @param array $arr
     * @return StdClass
     */
    public static function ArrayToObject($arr) {
        return is_array($arr) ? (object)array_map([__CLASS__, __FUNCTION__], $arr) : $arr;
    }

    /**
     * 判断对象/数组中是否存在key并且对应的值有效(不为false或null视为有效)
     * @param string $key
     * @param object|array $data
     * @return boolean
     */
    public static function Valid($key, $data) {
        if(!is_object($data)) $data = self::ArrayToObject($data);
        return array_key_exists($key, $data) && false !== $data->$key && !is_null($data->$key); 
    }
    
    /**
     *  创建签名数据
     * @param array $data
     * @param string $key 签名使用的key
     * @return string
     */
    public static function CreateSign(array $data, $key) {
        $data['_time'] = time();
        ksort($data);
        $data = base64_encode(json_encode($data));
        $sign = md5($data.'|'.$key);
        $sign = base64_encode($data.'|'.$sign);
        return $sign;
    }
    
    /**
     * 验证签名并返回数据
     * @param string $sign 签名后的数据
     * @param string $key 签名使用的key
     * @param number $expire 有效时长(秒)
     * @return boolean|array
     */
    public static function VerifySign($sign, $key, $expire = 1800) {
        $sign = explode('|', base64_decode($sign));
        if(count($sign) != 2) return false;
        $data = $sign[0];
        $sign = $sign[1];
        if(0 !== strcmp($sign, md5($data.'|'.$key))) return false;
        $data = json_decode(base64_decode($data), true);
        if(!is_array($data)) return false;
        if(time() - $data['_time'] > $expire) return false;
        return $data;
    }
    
    /**
     *
     * 检测当前请求客户端是否为移动端
     */
    public static function IsMobile() {
        if(false !== stripos($_SERVER['HTTP_USER_AGENT'], 'android') || false !== stripos($_SERVER['HTTP_USER_AGENT'], 'ios')
        || false !== stripos($_SERVER['HTTP_USER_AGENT'], 'wp')) return true;
        return false;
    }
    
    /**
     * 字符串截取
     * @param string $string
     * @param integer $length 截取长度
     * @param string $etc 后缀
     * @return string
     */
    public static function Truncate($string, $length, $etc = '...') {
        $string = preg_replace('/<br\s?\/?>/', ' ', $string);
        $string = strip_tags($string);
        $string = preg_replace('/\s+/', ' ', $string);
        $trancate = mb_substr(trim($string), 0, $length, 'utf-8');
        return strlen($trancate) < strlen($string) ? $trancate.$etc : $trancate;
    }
    
    /**
     * 高亮字符串
     * @param string $string
     * @param string $key 需要高亮的关键字
     * @param integer $length 截取长度
     * @param string $etc 后缀
     * @return string
     */
    public static function Highlight($string, $key, $length, $etc = '...') {
        if($key == '') return self::Truncate($string, $length, $etc);
        $string = preg_replace('/<br\s?\/?>/', ' ', $string);
        $string = strip_tags($string);
        $pos = stripos($string, $key) ? : 0;
        $pos = ($pos + strlen($key) - $length/2 - 1) > 0 ? ($pos + strlen($key) - $length/2 - 1) : 0;
        $trancate = mb_substr($string, $pos, $length, 'utf-8');
        $t = str_ireplace($key, '<mark>'.$key.'</mark>', $trancate);
        return strlen($trancate) < strlen($string) ? $t.$etc : $t;
    }
    
    /**
     * 提取数组中元素的某项属性作为数组的索引
     * @example
     * $data = [
     *      0 => ['a' => 2, 'b' => 1],
     *      1 => ['a' => 1, 'b' => 3],
     *      2 => ['a' => 4, 'b' => 2]
     * ];
     * 如果希望以每项元素中的属性a作为数组的索引，则如此调用：Kit::IndexRebuild('a', $data)，新$data：
     * $data = [
     *      2 => ['a' => 2, 'b' => 1],
     *      1 => ['a' => 1, 'b' => 3],
     *      4 => ['a' => 4, 'b' => 2]
     * ];
     * @param string $property 希望作为新索引的属性
     * @param array $data
     * @return array
     */
    public static function IndexRebuild($property, array $data) {
        $list = [];
        foreach($data as $v) {
            $_v = is_object($v) ? (array)$v : $v;
            $list[$_v[$property]] = $v;
        }
        return $list;
    }
    
    /**
     * 打印变量
     * @param mixed $variable 需要变量
     * @param boolean $halt 是否终止程序运行
     */
    public static function Dump($variable, $halt = true) {
        header('Content-Type:text/html;charset=utf-8');
        var_dump($variable);
        $halt && die;
    }
}
