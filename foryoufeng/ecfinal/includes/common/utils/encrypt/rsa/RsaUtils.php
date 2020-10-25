<?php
/**
 *加密解密工具
 * Created by PhpStorm.
 * User: root
 * Date: 11/17/16
 * Time: 9:49 AM
 * Vsersion:2.0.0
 */

class RsaUtils {
    private $private_key;
    private $public_key;

    public function __construct($private_key,$public_key)
    {
        //以下为了初始化私钥，保证在您填写私钥时不管是带格式还是不带格式都可以通过验证。
        $this->private_key=$private_key;
        $this->private_key=str_replace("-----BEGIN RSA PRIVATE KEY-----","",$this->private_key);
        $this->private_key=str_replace("-----END RSA PRIVATE KEY-----","",$this->private_key);
        $this->private_key=str_replace("\n","",$this->private_key);
        $this->private_key="-----BEGIN RSA PRIVATE KEY-----".PHP_EOL .wordwrap($this->private_key, 64, "\n", true). PHP_EOL."-----END RSA PRIVATE KEY-----";

        //以下为了初始化私钥，保证在您填写私钥时不管是带格式还是不带格式都可以通过验证。
        $this->public_key=$public_key;
        $this->public_key=str_replace("-----BEGIN PUBLIC KEY-----","",$this->public_key);
        $this->public_key=str_replace("-----END PUBLIC KEY-----","",$this->public_key);
        $this->public_key=str_replace("\n","",$this->public_key);
        $this->public_key='-----BEGIN PUBLIC KEY-----'.PHP_EOL.wordwrap($this->public_key, 64, "\n", true) .PHP_EOL.'-----END PUBLIC KEY-----';
    }

    /**
     * RSA签名
     * @param $data 待签名数据
     * @return string 签名结果
     */
    public function sign($data){
        $res=openssl_get_privatekey($this->private_key);

        if($res)
        {
            openssl_sign($data, $sign,$res);
        }
        else {
            echo "您的私钥格式不正确!"."<br/>"."The format of your private_key is incorrect!";
            exit();
        }
        openssl_free_key($res);
        //base64编码
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * RSA验签
     * @param $data 待签名数据
     * @param $sign 要校对的的签名结果
     * @return bool验证结果
     */
    public function verify($data,$sign){

        $res=openssl_get_publickey($this->public_key);
        if($res)
        {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
        else {
            echo "您的公钥格式不正确!"."<br/>"."The format of your public_key is incorrect!";
            exit();
        }
        openssl_free_key($res);
        return $result;
    }

}