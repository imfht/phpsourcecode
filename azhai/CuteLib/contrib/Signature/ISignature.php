<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Contrib\Signature;


/**
 * 加密工具
 */
interface ISignature
{
    /**
     * 获得名称
     * @return string 名称
     */
    public function getName();

    /**
     * 增加一些签名相关的字段和值
     */
    public function addFields(&$payment);

    /**
     * 加密
     * @param string $origin 明文
     * @return string 密文
     */
    public function sign($origin);

    /**
     * 验证
     * @param string $origin 明文
     * @param string $crypto 密文
     * @return bool
     */
    public function verify($origin, $crypto);
}
