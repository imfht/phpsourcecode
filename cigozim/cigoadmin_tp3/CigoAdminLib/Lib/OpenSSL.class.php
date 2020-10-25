<?php

namespace CigoAdminLib\Lib;

use Common\Lib\SystemConfigItem;
use Think\Exception;

class OpenSSL {
	public static function initRsaKey() {
		$config = array(
			"private_key_bits" => 2048,
			"private_key_type" => OPENSSL_KEYTYPE_RSA
		);
		$res = openssl_pkey_new($config);
		if ($res == false) return false;
		openssl_pkey_export($res, $privateKey);
		$publicKeyInfo = openssl_pkey_get_details($res);

		$privateKeyStr = preg_replace('/' . C(CigoGlobal::C_FLAG_PRIVATE_KEY_BEGIN) . '/i', '', $privateKey);
		$privateKeyStr = preg_replace('/' . C(CigoGlobal::C_FLAG_PRIVATE_KEY_END) . '/i', '', $privateKeyStr);

		$publicKeyStr = preg_replace('/' . C(CigoGlobal::C_FLAG_PUBLIC_KEY_BEGIN) . '/i', '', $publicKeyInfo["key"]);
		$publicKeyStr = preg_replace('/' . C(CigoGlobal::C_FLAG_PUBLIC_KEY_END) . '/i', '', $publicKeyStr);

		return array(
			SystemConfigItem::RSA_PRIVATE_KEY => trim(str_replace("\n", "", $privateKeyStr)),
			SystemConfigItem::RSA_PUBLIC_KEY => trim(str_replace("\n", "", $publicKeyStr))
		);
	}

	public static function rsaEncryptByPublicKey($rsaPublicKey, $plainStr, &$encryptedStr) {

		$publicKey = C(CigoGlobal::C_FLAG_PUBLIC_KEY_BEGIN) . trim($rsaPublicKey) . C(CigoGlobal::C_FLAG_PUBLIC_KEY_END);
		$keyResId = openssl_pkey_get_public($publicKey);

		openssl_public_encrypt(trim($plainStr), $encryptedStr, $keyResId);

		$encryptedStr = base64_encode($encryptedStr);
		$encryptedStr = trim(urlencode($encryptedStr));
	}

	public static function rsaDecryptByPrivateKey($rsaPrivateKey, $encryptedStr, &$plainStr) {
		$private_key = C(CigoGlobal::C_FLAG_PRIVATE_KEY_BEGIN) . trim($rsaPrivateKey) . C(CigoGlobal::C_FLAG_PRIVATE_KEY_END);
		$keyResId = openssl_pkey_get_private($private_key);

		$encryptedStr = urldecode(trim($encryptedStr));
		$encryptedStr = base64_decode($encryptedStr);

		openssl_private_decrypt($encryptedStr, $plainStr, $keyResId);
	}

	public static function rsaEncryptByPrivateKey($rsaPrivateKey, $plainStr, &$encryptedStr) {
		$private_key = C(CigoGlobal::C_FLAG_PRIVATE_KEY_BEGIN) . trim($rsaPrivateKey) . C(CigoGlobal::C_FLAG_PRIVATE_KEY_END);
		$keyResId = openssl_pkey_get_private($private_key);

		openssl_private_encrypt(trim($plainStr), $encryptedStr, $keyResId);

		$encryptedStr = base64_encode($encryptedStr);
		$encryptedStr = trim(urlencode($encryptedStr));
	}

	public static function rsaDecryptByPublicKey($rsaPublicKey, $encryptedStr, &$plainStr) {
		$publicKey = C(CigoGlobal::C_FLAG_PUBLIC_KEY_BEGIN) . trim($rsaPublicKey) . C(CigoGlobal::C_FLAG_PUBLIC_KEY_END);
		$keyResId = openssl_pkey_get_public($publicKey);

		$encryptedStr = urldecode(trim($encryptedStr));
		$encryptedStr = base64_decode($encryptedStr);

		openssl_public_decrypt($encryptedStr, $plainStr, $keyResId);
	}

	public static function getRandomString($length = 32) {
		if (function_exists('openssl_random_pseudo_bytes')) {
			$bytes = openssl_random_pseudo_bytes($length);

			if ($bytes === false)
				throw new Exception('Unable to generate a random string');

			return trim(substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length));
		}

		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		return trim(substr(str_shuffle(str_repeat($pool, 5)), 0, $length));
	}

	public static function getToken($len = 32) {
		$hashStr = hash('sha256', OpenSSL::getRandomString($len * 2), false);
		return trim(substr($hashStr, 0, $len));
	}

	public static function aesEncrypt($plainStr, $key) {
		$encryptString = openssl_encrypt(trim($plainStr), C(CigoGlobal::C_FLAG_AES_METHODS), $key, false);
		return trim(urlencode($encryptString));
	}

	public static function aesDescrypt($encryptedStr, $key) {
		$encryptedStr = urldecode(trim($encryptedStr));
		return openssl_decrypt($encryptedStr, C(CigoGlobal::C_FLAG_AES_METHODS), $key, false);
	}
}