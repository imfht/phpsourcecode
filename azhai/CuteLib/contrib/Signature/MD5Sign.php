<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Contrib\Signature;

use \Cute\Contrib\Signature\ISignature;


/**
 * MD5加密
 */
class MD5Sign implements ISignature
{
    protected $secrecy = ''; // 密钥

    public function __construct($secrecy)
    {
        $this->secrecy = $secrecy;
    }

    public function getName()
    {
        return 'MD5';
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
        return md5($origin . $this->secrecy);
    }
}
