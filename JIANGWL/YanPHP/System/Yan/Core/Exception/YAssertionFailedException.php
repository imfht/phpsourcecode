<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/8/25
 * Time: 19:40
 */

namespace Yan\Core\Exception;

use \Assert\InvalidArgumentException;

class YAssertionFailedException extends InvalidArgumentException  implements YanExceptionInterface
{
}