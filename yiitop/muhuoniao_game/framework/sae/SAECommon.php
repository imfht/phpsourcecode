<?php
class SAECommon
{
    /*
    * 获得sae平台的更目录 apps/appname/version
    * 如我的是 apps/yiis/1 
    */
    function get_sae_root()
    {
        return dirname(dirname(dirname(__FILE__))).'/'; 
    }
    /*
    * 加密函数，原使用网上的dz加密函数，后发现部分assets是 加密后的目录url 再加上 文件名
    * 所以改成隐藏系统目录的办法
    */
    function saedisk_encrypt($txt, $key = '<yiis>')
    {
        $dir = $this->get_sae_root();
        $txt = str_replace($dir,'',$txt);
        return $txt;
	    #srand((double)microtime() * 1000000);
	    #$encrypt_key = md5(rand(0, 32000));
	    $encrypt_key = md5($key);
	    $ctr = 0;
	    $tmp = '';
	    for($i = 0;$i < strlen($txt); $i++) {
	    $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
	    $tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);
	    }
	    $str = base64_encode(passport_key($tmp, $key));
        return $str;
    }
    /*
    * 解密函数
    * 同上
    */
    function saedisk_decrypt($txt, $key = '<yiis>')
    {
        $dir = $this->get_sae_root();
        $txt = $dir.$txt;
        return $txt;

	    $txt = passport_key(base64_decode($txt), $key);
	    $tmp = '';
	    for($i = 0;$i < strlen($txt); $i++) {
	    $md5 = $txt[$i];
	    $tmp .= $txt[++$i] ^ $md5;
	    }
	    return $tmp;
    }
    /*
    * 密钥函数
    */
    function passport_key($txt, $encrypt_key)
    {
	    $encrypt_key = md5($encrypt_key);
	    $ctr = 0;
	    $tmp = '';
	    for($i = 0; $i < strlen($txt); $i++) {
	    $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
	    $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
	    }
	    return $tmp;
    }
}
