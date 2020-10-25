<?php

namespace fluiex\util;

/**
 * 据说是Discuz对PHP社区最大的贡献
 */
class AuthCode
{
    /**
     *
     * @var string 
     */
    protected $key;
    
    /**
     * @var int 盐值长度
     */
    protected $saltLength = 4;

    /**
     * 
     * @param string $key 传过来的值会被md5,以便确保长度是32位
     */
    public function __construct($key)
    {
        $this->key = md5($key);
    }

    /**
     * 编码
     * @param string $string
     * @param int $expiry
     * @return string
     */
    public function encode($string, $expiry = 0)
    {
        $salt = $this->saltLength ? substr(md5(microtime()), -$this->saltLength) : '';

        $cryptkey = $this->cryptKey($this->key, $salt);

        $string = sprintf('%010d', $expiry ? $expiry + time() : 0) 
                . substr(md5($string . $this->keyb($this->key)), 0, 16) . $string;

        $result = $this->crypt($string, $cryptkey);

        return $salt . str_replace('=', '', base64_encode($result));
    }
    
    /**
     * 解码
     * @param string $string
     * @return string
     */
    public function decode($string)
    {
        $salt = $this->saltLength ? substr($string, 0, $this->saltLength) : '';

        $cryptkey = $this->cryptKey($this->key, $salt);

        $string = base64_decode(substr($string, $this->saltLength));
        
        $result = $this->crypt($string, $cryptkey);
        
        if ((substr($result, 0, 10) == 0 
            || substr($result, 0, 10) - time() > 0) 
            && substr($result, 10, 16) == substr(md5(substr($result, 26) . $this->keyb($this->key)), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
        
    }
    
    private function keyb($key)
    {
        return md5(substr($key, 16, 16));
    }
    
    private function cryptKey($key, $salt)
    {
        $keya = md5(substr($key, 0, 16));
        return $keya . md5($keya . $salt);
    }
    
    private function crypt($string, $cryptkey)
    {
        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % strlen($cryptkey)]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < strlen($string); $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        
        return $result;
    }

}
