<?php  

/**
 * DES加密类
 * 
 * @author zongjl
 * @date 2018-08-29
 */   
class DesCrypt {
    
    private $key;
    
    public function __construct($key='') {
        $this->key = $key;    
    }
    
    public function encrypt($str, $key='')   {
        if (!$key) {
            $key = $this->key;
        }
        $block = mcrypt_get_block_size('des', 'ecb');
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $code16 = mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
        return base64_encode($code16);
    } 
    
    public function decrypt($str, $key='')  {
        if (!$key) {
            $key = $this->key;
        }
        $str = base64_decode($str);
        $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
        $block = mcrypt_get_block_size('des', 'ecb');
        $pad = ord($str[($len = strlen($str)) - 1]);
        return substr($str, 0, strlen($str) - $pad);
    }
}
?>