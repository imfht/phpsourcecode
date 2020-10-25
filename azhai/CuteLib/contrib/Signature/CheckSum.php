<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Contrib\Signature;

use \Cute\Contrib\Signature\ISignature;


/**
 * 检验和，取末尾4位hex转化为10进制，并且最后一位不能为0
 */
class CheckSum implements ISignature
{
    protected $secrecy = ''; // 密钥
    protected $hex_size = 0; // 16进制长度
    protected $end_nz = false; // 最后一位不能为0

    public function __construct($secrecy, $hex_size = 4, $end_nz = false)
    {
        $this->secrecy = $secrecy;
        $this->hex_size = $hex_size;
        $this->end_nz = $end_nz;
    }

    public function getName()
    {
        return 'CheckSum';
    }

    public function addFields(&$payment)
    {
    }

    public function verify($origin, $crypto)
    {
        return $this->sign($origin) === $crypto;
    }

    public function sign($origin)
    {
        $checksum = md5($origin . $this->secrecy);
        $crypto = intval(substr($checksum, -$this->hex_size), 16);
        if ($this->end_nz && $crypto % 10 === 0) { //以0结尾
            $max_hex = pow(16, $this->hex_size);
            $crypto = $max_hex + rtrim(strval($crypto), '0');
        }
        return sprintf('%0' . $this->getDecSize() . 'd', $crypto);
    }

    public function getDecSize()
    {
        return strlen(strval(pow(16, $this->hex_size)));
    }
}
