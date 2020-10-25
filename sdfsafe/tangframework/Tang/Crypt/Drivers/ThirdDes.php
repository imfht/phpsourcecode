<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Crypt\Drivers;
/**
 * 3des加密
 * Class ThirdDes
 * @package Tang\Crypt\Drivers
 */
class ThirdDes implements ICryptDriver
{
    /**
     * 密钥
     * @var string
     */
    protected $key;
    /**
     * 向量
     * @var string
     */
    protected $iv;
    /**
     * 向量长度
     * @var int
     */
    protected $ivLength = 8;
    /**
     * 密钥长度
     * @var int
     */
    protected $keyLength = 24;
    /**
     * 加密类型
     * @var string
     */
    protected $type = MCRYPT_3DES;

    /**
     * @param string $key
     * @param string $iv
     */
    public function __construct($key='',$iv='')
    {
        $this->setKey($key);
        $this->setIv($iv);
    }

    /**
     * 设置密钥
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $this->paddingString('k',$key,$this->keyLength);
        return $this;
    }

    /**
     * 设置向量
     * @param $iv
     * @return $this
     */
    public function setIv($iv)
    {
        $this->iv = $this->paddingString('v',$iv,$this->ivLength);;
        return $this;
    }

    /**
     * 获取密钥
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * 获取向量
     * @return string
     */
    public function getIv()
    {
        return $this->iv;
    }

    /**
     * @see ICryptDriver::encode()
     */
    public function encode($data,$expire=0)
    {
        $expire = sprintf('%010d',$expire?$expire+time():0);
        $data = $this->paddingPKCS7($expire.$data);
        $mcryptModule = $this->createMcryptModule();
        $encryptedData = mcrypt_generic($mcryptModule, $data);
        $encryptedData = base64_encode($encryptedData);
        $this->closeMcrypt($mcryptModule);
        return $encryptedData;
    }

    /**
     * @see ICryptDriver::decode()
     */
    public function decode($data)
    {
        $data = base64_decode($data);
        $mcryptModule = $this->createMcryptModule();
        $decodeString = mdecrypt_generic($mcryptModule,$data);
        $this->closeMcrypt($mcryptModule);
        $decodeString = $this->unPaddingPKCS7(trim($decodeString));
        $expire = substr($decodeString,0,10);
        if($expire > 0 && $expire < time())
        {
            return '';
        }
        return substr($decodeString,10);
    }

    /**
     * 创建Mcrypt
     * @return resource
     */
    private function createMcryptModule()
    {
        $mcryptModule = mcrypt_module_open($this->type,'',MCRYPT_MODE_CBC,'');
        mcrypt_generic_init($mcryptModule,$this->key,$this->iv);
        return $mcryptModule;
    }

    /**
     * 关闭
     * @param $mcryptModule
     */
    private function closeMcrypt($mcryptModule)
    {
        mcrypt_generic_deinit($mcryptModule);
        mcrypt_module_close($mcryptModule);
    }

    /**
     * PKCS7
     * @param $data
     * @return bool|string
     */
    private function unPaddingPKCS7($data)
    {
        $length = strlen($data);
        $paddingOrd = ord($data[$length - 1]);
        if ($paddingOrd > $length)
        {
            return false;
        }
        if (strspn($data,chr($paddingOrd), $length - $paddingOrd) != $paddingOrd)
        {
            return false;
        }
        return substr($data, 0, -1 * $paddingOrd);
    }

    /**
     * PKCS7补位操作
     * @param string $data
     * @return string
     */
    private function paddingPKCS7($data)
    {
        $blockSize = mcrypt_get_block_size(MCRYPT_3DES, 'cbc');
        $paddingOrd = $blockSize - (strlen($data) % $blockSize);
        $data .= str_repeat(chr($paddingOrd), $paddingOrd);
        return $data;
    }

    private function paddingString($char,$string,$allLength)
    {
        if(!$string)
        {
            $string = str_pad($string,$allLength,$char);
        } else
        {
            $length = strlen($string);
            if($length > $allLength)
            {
                $string = substr($string,0,$allLength);
            } else if($length < $allLength)
            {
                $string = str_pad($string,$allLength,$char);
            }
        }
        return $string;
    }
}