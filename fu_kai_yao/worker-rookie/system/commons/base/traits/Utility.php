<?php
namespace system\commons\base\traits;

use workerbase\classs\Config;
use workerbase\traits\Tools;

/**
 * 常用的一些辅助函数
 * 这里的函数大部分是与一些具体的逻辑相关的，不是纯工具函数
 * trait Utility
 * @author fukaiyao
 */
trait Utility
{
    use Tools;

    /**
     * 通讯加密 (生成加密的带签名的请求参数，发送给客户端，客户端进行相关解密，得到参数)
     * @param $rsaPublicKey -rsa公钥
     * @param array $arguments 要加密的参数
     * @return array ['data'=>加密密文, 'timestamp'=>当前时间戳, 'sign' => 签名]
     */
    public function encryptRequest($rsaPublicKey, array $arguments)
    {
        $paramStr = json_encode($arguments);

        $reqParams = array(
            'data' => $this->rsaEncrypt($rsaPublicKey, $paramStr),//rsa加密请求参数
            'timestamp' => time(),
        );

        //签名
        $reqParams['sign'] = $this->createOpenSign($reqParams, Config::read("secret", "config"));

        return $reqParams;
    }
}