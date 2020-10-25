<?php
namespace com\wxpay;
/**
 * 微信支付API异常类
 *
 * Class WxPayException
 * @package \com\wxpay
 * @author goldeagle
 */
class WxPayException extends \Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}