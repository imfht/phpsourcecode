<?php

/*密码验证,加密、解密*/

class SimpleEncrypt
{
    private $now = null;

    //构造方法
    public function __construct()
    {
        $this->now = date('Y-m-d H:i:s');
    }

    /**
     * 先加密，再进行base64代码
     * @param  string $text        原字符串
     * @param  string $encrypt_key 用于加密的key
     * @return string              加密串
     */
    public function encode($text, $encrypt_key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $c_str = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $encrypt_key, $text, MCRYPT_MODE_ECB, $iv);
        $encrypt_code = base64_encode($c_str);
        return $encrypt_code;
    }

    /**
     * 先进行base64解码，再解码
     * @param  string $encrypt_code 加密串
     * @param  string $encrypt_key  用于解密的key
     * @return string               解密后的字符串
     */
    public function decode($encrypt_code, $encrypt_key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $text = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $encrypt_key, base64_decode($encrypt_code), MCRYPT_MODE_ECB, $iv);
        return $text;
    }
}
