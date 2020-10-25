<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo;

/**
 * 助手类
 *
 * Class Helper
 * @package Timo
 */
class Helper
{
    /**
     * 创建文件夹
     *
     * @param string $path
     * @param int $mode
     */
    static function mkFolders($path, $mode = 0755)
    {
        if (!is_dir($path)) {
            mkdir($path, $mode, true);
        }
    }

    /**
     * 根据文件名创建文件
     *
     * @param string $file_name
     * @param int $mode
     * @return bool
     */
    static function mkFile($file_name, $mode = 0775)
    {
        if (!file_exists($file_name)) {
            $file_path = dirname($file_name);
            static::mkFolders($file_path, $mode);

            $fp = fopen($file_name, 'w+');
            if ($fp) {
                fclose($fp);
                chmod($file_name, $mode);
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * 生成四层亿级路径
     *
     * @param int $id
     * @param string $path_name
     * @return string
     *
     * 例: id = 1000189 生成 001/00/01/89
     */
    static function getFourPath($id, $path_name = '')
    {
        $id = (string)abs($id);
        $id = str_pad($id, 9, '0', STR_PAD_LEFT);
        $dir1 = substr($id, 0, 3);
        $dir2 = substr($id, 3, 2);
        $dir3 = substr($id, 5, 2);

        return ($path_name ? $path_name . '/' : '') . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($id, -2) . '/';
    }

    /**
     * 获取文件扩展名
     *
     * @param string $filename 文件名
     * @return string
     */
    static function getFileExt($filename)
    {
        $file_info = pathinfo($filename);
        return $file_info['extension'];
    }

    /**
     * 字符串加密解密算法
     *
     * @param string $string
     * @param string $operation
     * @param string $key
     * @param int $expiry
     * @return string
     */
    public static function authCode($string, $operation = 'DECODE', $key = '0D79A89479zka89z9z9i', $expiry = 0)
    {
        $c_key_length = 4;
        $key = md5($key);

        $key_a = md5(substr($key, 0, 16));
        $key_b = md5(substr($key, 16, 16));
        $key_c = $c_key_length ? ($operation == 'DECODE' ? substr($string, 0, $c_key_length) :
            substr(md5(microtime()), -$c_key_length)) : '';

        $crypt_key = $key_a . md5($key_a . $key_c);
        $key_length = strlen($crypt_key);

        $string = $operation == 'DECODE' ?
            base64_decode(substr($string, $c_key_length)) :
            sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $key_b), 0, 16) . $string;

        $result = [];
        $box = range(0, 255);
        $string_length = strlen($string);

        $rnd_key = [];
        for ($i = 0; $i <= 255; $i++) {
            $rnd_key[$i] = $crypt_key[$i % $key_length];
        }
        $rnd_key = array_map('ord', $rnd_key);

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rnd_key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        $p1 = $p2 = [];
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $p1[] = $string[$i];
            $p2[] = $box[($box[$a] + $box[$j]) % 256];
        }

        if (!empty($p1)) {
            $p1 = array_map('ord', $p1);
            foreach ($p1 as $k => $pv) {
                $result[] = $pv ^ $p2[$k];
            }

            unset($p1, $p2, $box, $tmp, $rnd_key);
            $result = implode('', array_map('chr', $result));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
                substr($result, 10, 16) == substr(md5(substr($result, 26) . $key_b), 0, 16)
            ) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $key_c . str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * 返回一个指定长度的随机数
     *
     * @param int $length
     * @param bool $numeric
     * @return string
     */
    public static function random($length, $numeric = false)
    {
        $seed = md5(print_r($_SERVER, 1) . microtime(true));
        if ($numeric) {
            $seed = str_replace('0', '', base_convert($seed, 16, 10)) . '0123456789';
        } else {
            $seed = base_convert($seed, 16, 35) . 'zZz' . strtoupper($seed);
        }

        $hash = '';
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed[mt_rand(0, $max)];
        }

        return $hash;
    }

    /**
     * 生成随机16进制数
     *
     * @param $length
     * @return string
     */
    public static function randomHex($length)
    {
        $str = '0123456789abcdef';
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= $str[mt_rand(0, 15)];
        }
        return $random;
    }
}
