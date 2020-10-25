<?php
namespace herosphp\rsa;
/**
 * RSA加解密类
 * “参数签名”用私钥加密，“验证签名”用公钥解密
 * “内容加密”用公钥加密，“内容解密”用私钥解密
 * @author yangjian<yangjian102621@gmail.com>
 * @since v3.0.0
 */

class RSACrypt {

	public $pubkey; //公钥
	public $privkey; //私钥

	function __construct() {

		$this->pubkey  = file_get_contents(getConfig('rsa_public_key'));
		$this->privkey = file_get_contents(getConfig('rsa_private_key'));
	}

    /**
     * 私钥加密
     * @param $data
     * @return mixed|string
     */
	public  function encryptByPrivateKey($data) {
		$pi_key =  openssl_pkey_get_private($this->privkey);
		$encrypted="";
		openssl_private_encrypt($data,$encrypted,$pi_key,OPENSSL_PKCS1_PADDING);//私钥加密
		$encrypted = self::urlsafe_b64encode($encrypted);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
		return $encrypted;
	}

    /**
     * 公钥解密
     * @param $data
     * @return string
     */
	public  function decryptByPublicKey($data) {
		$pu_key =  openssl_pkey_get_public($this->pubkey);
		$decrypted = "";
		$data = self::urlsafe_b64decode($data);

		openssl_public_decrypt($data,$decrypted,$pu_key);//公钥解密

		return $decrypted;
	}

    /**
     * 公钥加密
     * @param $data
     * @return mixed|string
     */
	public  function encryptByPublicKey($data) {
		$pu_key  = openssl_pkey_get_public($this->pubkey);
		$encrypted="";
		openssl_public_encrypt($data,$encrypted,$pu_key,OPENSSL_PKCS1_PADDING);//公钥加密
		$encrypted = self::urlsafe_b64encode($encrypted);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
		return $encrypted;
	}

    /**
     * 私钥解密
     * @param $data
     * @return string
     */
	public  function decryptByPrivateKey($data) {
		$pi_key =  openssl_pkey_get_private($this->privkey);
		$decrypted = "";
		$data = self::urlsafe_b64decode($data);
		openssl_private_decrypt($data,$decrypted,$pi_key);//私钥解密
		return $decrypted;
	}

    /**
     * 安全的b64encode
     * @param $string
     * @return mixed|string
     */
	public static  function urlsafe_b64encode($string) {
		$data = base64_encode($string);
		$data = str_replace(array('+','/','='),array('-','_','@'),$data);
		return $data;
	}

    /**
     * 安全的b64decode
     * @param $string
     * @return string
     */
	public static function urlsafe_b64decode($string) {
		$data = str_replace(array('-','_','@'),array('+','/','='),$string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}
}


