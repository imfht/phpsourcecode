<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/8/31
 * Time: 21:43
 */

namespace Yan\Core;

use Assert\Assertion as BaseAssertion;

class YAssert extends BaseAssertion
{
    protected static $exceptionClass = 'Yan\Core\Exception\YAssertionFailedException';

    /**
     * Helper method that handles building the assertion failure exceptions.
     * They are returned from this method so that the stack trace still shows
     * the assertions method.
     *
     * @param mixed           $value
     * @param string|callable $message
     * @param int             $code
     * @param string|null     $propertyPath
     * @param array           $constraints
     *
     * @return mixed
     */
    protected static function createException($value, $message, $code, $propertyPath = null, array $constraints = array())
    {
        $exceptionClass = static::$exceptionClass;

        return new $exceptionClass($message, ReturnCode::INVALID_ARGUMENT, $propertyPath, $value, $constraints);
    }
}