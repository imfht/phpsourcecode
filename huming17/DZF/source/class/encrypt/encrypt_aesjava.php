<?php
class encrypt_aesjava {
	public static function encrypt($input, $key) {
	$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
	$input = encrypt_aesjava::pkcs5_pad($input, $size);
	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
	$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	mcrypt_generic_init($td, $key, $iv);
	$data = mcrypt_generic($td, $input);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	$data = base64_encode($data);
	return $data;
	}
 
	private static function pkcs5_pad ($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}
 
	public static function decrypt($sStr, $sKey) {
		$decrypted= mcrypt_decrypt(
		MCRYPT_RIJNDAEL_128,
		$sKey,
		base64_decode($sStr),
		MCRYPT_MODE_ECB
	);
 
		$dec_s = strlen($decrypted);
		$padding = ord($decrypted[$dec_s-1]);
		$decrypted = substr($decrypted, 0, -$padding);
		return $decrypted;
	}	
}
/*
$key = "1234567891234567";
$data = "example";
 
$value = encrypt_aesjava::encrypt($data , $key );
echo $value.'<br/>';
echo encrypt_aesjava::decrypt($value, $key );
 
---------------
java 代码
 
 
 
import javax.crypto.Cipher;
import javax.crypto.spec.SecretKeySpec;
 
import org.apache.commons.codec.binary.Base64;
 
public class encrypt_aesjava {
	public static String encrypt(String input, String key){
	byte[] crypted = null;
	try{
	SecretKeySpec skey = new SecretKeySpec(key.getBytes(), "AES");
	Cipher cipher = Cipher.getInstance("AES/ECB/PKCS5Padding");
	cipher.init(Cipher.ENCRYPT_MODE, skey);
	crypted = cipher.doFinal(input.getBytes());
	}catch(Exception e){
	System.out.println(e.toString());
	}
	return new String(Base64.encodeBase64(crypted));
}
 
public static String decrypt(String input, String key){
	byte[] output = null;
	try{
	SecretKeySpec skey = new SecretKeySpec(key.getBytes(), "AES");
	Cipher cipher = Cipher.getInstance("AES/ECB/PKCS5Padding");
	cipher.init(Cipher.DECRYPT_MODE, skey);
	output = cipher.doFinal(Base64.decodeBase64(input));
	}catch(Exception e){
	System.out.println(e.toString());
	}
	return new String(output);
}
 
	public static void main(String[] args) {
		String key = "1234567891234567";
		String data = "example";
		
		System.out.println(encrypt_aesjava.encrypt(data, key));
		
		System.out.println(encrypt_aesjava.decrypt(encrypt_aesjava.encrypt(data, key), key));
		
			
	}	
}
*/