<?php
class SysCrypt {

	private $crypt_key;

	// 构造函数 
	public function __construct($crypt_key) {
	   $this -> crypt_key = $crypt_key;
	}
	public function php_encrypt($txt) {
	   srand((double)microtime() * 1000000);
	   $encrypt_key = md5(rand(0,32000));
	   $ctr = 0;
	   $tmp = '';
	   for($i = 0;$i<strlen($txt);$i++) {
	    $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
	    $tmp .= $encrypt_key[$ctr].($txt[$i]^$encrypt_key[$ctr++]);
	   }
	   return base64_encode(self::__key($tmp,$this -> crypt_key));
	}
	
	public function php_decrypt($txt) {
	   $txt = self::__key(base64_decode($txt),$this -> crypt_key);
	   $tmp = '';
	   for($i = 0;$i < strlen($txt); $i++) {
	    $md5 = $txt[$i];
	    $tmp .= $txt[++$i] ^ $md5;
	   }
	   return $tmp;
	}
	
	private function __key($txt,$encrypt_key) {
	   $encrypt_key = md5($encrypt_key);
	   $ctr = 0;
	   $tmp = '';
	   for($i = 0; $i < strlen($txt); $i++) {
	    $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
	    $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
	   }
	   return $tmp;
	}
	
	public function __destruct() {
	   $this -> crypt_key = null;
	}
}
/*
$sc = new SysCrypt('phpwms');
$text = '110';
print($sc -> php_encrypt($text));
print('<br>');
print($sc -> php_decrypt($sc -> php_encrypt($text)));
*/