<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 4/16/18
 * Time: 1:04 PM
 */

namespace Kernel\Core\Exception;


use Throwable;

class RouteNotFoundException extends \Exception
{
    public function __construct(string $message = "", int $code = ErrorCode::ROUTE_NOT_FOUND, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}