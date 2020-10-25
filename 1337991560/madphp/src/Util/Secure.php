<?php

/**
 * Secure
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Util;

class Secure
{
    const CRYPT_KEY = 'ms';

    /**
     * 字符串加密/解密处理
     * @param $string
     * @param string $operation
     * @param string $key
     * @param int $expiry
     * @return string
     */
    static function handle($string, $operation = 'encrypt', $key = '', $expiry = 0)
    {
        $key_length = 4;
        $key = md5($key != '' ? $key : self::CRYPT_KEY);
        $fixedkey = md5($key);
        $egiskeys = md5(substr($fixedkey, 16, 16));
        $runtokey = $key_length ? ($operation == 'encrypt' ? substr(md5(microtime(true)), - $key_length) : substr($string, 0, $key_length)) : '';
        $keys = md5(substr($runtokey, 0, 16) . substr($fixedkey, 0, 16) . substr($runtokey, 16) . substr($fixedkey, 16));
        $string = $operation == 'encrypt' ? sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $egiskeys), 0, 16) . $string : base64_decode(substr($string, $key_length));

        $i = 0;
        $result = '';
        $string_length = strlen($string);
        for ($i = 0; $i < $string_length; $i ++) {
            $result .= chr(ord($string{$i}) ^ ord($keys{$i % 32}));
        }
        if ($operation == 'encrypt') {
            return $runtokey . str_replace('=', '', base64_encode($result));
        } else {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $egiskeys), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        }
    }

    /**
     * 字符串加密
     * @param $string
     * @param string $key
     * @param int $expiry
     * @return string
     */
    static function encrypt($string, $key = '', $expiry = 0)
    {
        return self::handle($string, 'encrypt', $key, $expiry);
    }

    /**
     * 字符串解密
     * @param $string
     * @param string $key
     * @param int $expiry
     * @return string
     */
    static function decrypt($string, $key = '', $expiry = 0)
    {
        return self::handle($string, 'decrypt', $key, $expiry);
    }
}