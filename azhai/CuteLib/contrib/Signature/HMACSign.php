<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Contrib\Signature;

use \Cute\Contrib\Signature\ISignature;


/**
 * HMAC加密
 */
class HMACSign implements ISignature
{
    protected $secrecy = ''; // 密钥

    public function __construct($secrecy)
    {
        $this->secrecy = $secrecy;
    }

    public function getName()
    {
        return 'HMAC';
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
        return hash_hmac('md5', convert($origin, 'UTF-8'), $this->secrecy);
    }
}
