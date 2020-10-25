<?php
/**
 * 字符串工具类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v1.2.1
 */
namespace herosphp\string;

use herosphp\lock\SynLockFactory;

class StringUtils {

    const UUID_LOCK_KEY = 'herosphp_uuid_lock_key';

    /**
     * 生成一个唯一分布式UUID,根据机器不同生成. 长度为18位。
     * 机器码(2位) + 时间(12位，精确到微秒)
     * @return mixed
     */
    public static function genGlobalUid() {

        $lock = SynLockFactory::getFileSynLock(self::UUID_LOCK_KEY);
        $lock->tryLock();
        usleep(5);
        //获取服务器时间，精确到毫秒
        $tArr = explode(' ', microtime());
        $tsec = $tArr[1];
        $msec = $tArr[0];
        if ( ($sIdx = strpos($msec, '.')) !== false ) {
            $msec = substr($msec, $sIdx + 1);
        }

        //获取服务器节点信息
        if ( !defined('SERVER_NODE') ) {
            $node = 0x01;
        } else {
            $node = SERVER_NODE;
        }
        $lock->unlock();

        return sprintf(
            "%02x%08x%08x",
            $node,
            $tsec,
            $msec
        );
    }

    /**
     * 将中文数组json编码
     * @param $array
     * @return string
     */
    public static function jsonEncode($array) {
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 中文 json 数据解码
     * @param $string
     * @return mixed
     */
    public static function jsonDecode($string) {
        $string = trim($string, "\xEF\xBB\xBF");
        return json_decode($string, true);
    }

    /**
     * 下划线转驼峰
     * @param $str
     * @return string
     */
    public static function underline2hump($str) {

        $str = trim($str);
        if ( strpos($str, "_") === false ) return $str;

        $arr = explode("_", $str);
        $__str = $arr[0];
        for( $i = 1; $i < count($arr); $i++ ) {
            $__str .= ucfirst($arr[$i]);
        }
        return $__str;
    }

    /**
     * 驼峰转下划线
     * @param $str
     * @return mixed
     */
    public static function hump2Underline($str) {
        $arr = array();
        for( $i = 1; $i < strlen($str); $i++ ) {
            if ( ord($str[$i]) > 64 && ord($str[$i]) < 91 ) {
                $arr[] = "_".strtolower($str[$i]);
            } else {
                $arr[] = $str[$i];
            }
        }
        return implode('', $arr);
    }

    /**
     * 将16进制的颜色转成成RGB
     * @param string $hexColor
     * @return array
     */
    public static function hex2rgb($hexColor) {

        $color = str_replace('#', '', $hexColor);
        //1.六位数表示形式
        if ( strlen($color) > 3 ) {
            $rgb = array(
                'r' => hexdec(substr($color, 0, 2)),
                'g' => hexdec(substr($color, 2, 2)),
                'b' => hexdec(substr($color, 4, 2))
            );

            //2. 三位数表示形式
        } else {
            $color = $hexColor;
            $r = substr($color, 0, 1) . substr($color, 0, 1);
            $g = substr($color, 1, 1) . substr($color, 1, 1);
            $b = substr($color, 2, 1) . substr($color, 2, 1);
            $rgb = array(
                'r' => hexdec($r),
                'g' => hexdec($g),
                'b' => hexdec($b)
            );
        }
        return $rgb;
    }

    /**
     * 生成随机字符串
     * @param $length
     * @return string
     */
    public static function genRandomString($length) {
        $letters = array('1','2','3','4','5','6','7','8','9','0',
            'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o',
            'p','q','r','s','t','u','v','w','x','y','z','A','B','C','D',
            'E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S',
            'T','U','V','W','X','Y','Z');
        $str = array();
        $count = count($letters);
        while ($length-- > 0) {
            $str[] = $letters[mt_rand() % $count];
        }
        return implode('', $str);
    }

    /**
     * 根据明文和盐生成密码
     * @param $src
     * @param $salt
     * @return string
     */
    public static function generatePassword($src, $salt) {
        return md5($src.md5($salt));
    }
} 