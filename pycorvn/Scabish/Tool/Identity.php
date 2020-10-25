<?php
namespace Scabish\Tool;

use SCS;

/**
 * Scabish\Tool\Indentify
 * 身份加密验证类
 * 
 * @example
 * $id = 11; //当前ID
 * 加密：
 * $encrypted = \Scabish\Tool\Identify\Instance('keybalabala')->Set($id);
 * 解密：
 * \Scabish\Tool\Identify\Instance('keybalabala')->GetId($encrypted); // 结果为11
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2015-01-24
 */
class Identity {
    
    private static $_instance;
    
    private $_key;
    
    public function __construct() {}
    
    /**
     * 获取Identity的实例
     * @param string $key 加密密钥
     * @return \Scabish\Tool\Identity
     */
    public static function Instance($key) {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
            self::$_instance->_key = $key;
        }
        return self::$_instance;
    }
    
    /**
     * 
     * 加密唯一标识ID
     * @param string 唯一标识$id
     * @return 加密后的字符串
     */
    public function Set($id) {
        $time = time();
        return $this->Encrypt($id.'|'.$time.'|'.md5($id.'|'.$time.'|'.$this->_key));
    }
    
    /**
     * 获取当前已加密过的字符串中隐含的ID
     * @return string 唯一标识ID
     */
    public function Get($string) {
        $decrypt = $this->decrypt($string);
        $decrypt = explode('|', $decrypt);
        if(!isset($decrypt[2])) return false;
        list($id, $time, $sign) = $decrypt;
        if(0 !== strcasecmp(md5($id.'|'.$time.'|'.$this->_key), $sign)) return fasle;
        
        return $decrypt[0];
    }
    
    
    /**
     * 返回以当前用户ID、IP经系统加密函数加密后的字符串
     * @param integer $id 唯一标识ID
     * @return string 加密后的字符串
     */
    private function Encrypt($text) {
        srand((double)microtime() * 1000000);
        $encrypt_key = md5(rand(0, 32000));
        $ctr = 0;
        $tmp = '';
        for($i = 0; $i < strlen($text); $i++) {
            if($ctr == strlen($encrypt_key)) $ctr = 0;
            $tmp .= substr($encrypt_key, $ctr, 1).(substr($text, $i, 1) ^ substr($encrypt_key, $ctr, 1));
            $ctr++;
        }
        return base64_encode($this->keyED($tmp));
    }
    
    /**
     * 字符串解密
     * @param string $text 加密字符串
     * @return 解密后的字符串
     */
    private function Decrypt($text) {
        $text = $this->keyED(base64_decode($text));
        $tmp = '';
        for($i = 0; $i < strlen($text); $i++) {
            $md5 = substr($text, $i, 1);
            $i++;
            $tmp .= (substr($text, $i, 1) ^ $md5);
        }
        return $tmp;
    }
    
    private function KeyED($text) {
        $encrypt_key = md5($this->_key);
        $ctr = 0;
        $tmp = '';
        for($i = 0; $i < strlen($text); $i++) {
            if($ctr == strlen($encrypt_key)) $ctr=0;
            $tmp .= substr($text, $i, 1) ^ substr($encrypt_key, $ctr, 1);
            $ctr++;
        }
        return $tmp;
    }
}