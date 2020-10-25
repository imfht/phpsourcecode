<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 4/16/18
 * Time: 1:03 PM
 */

namespace Kernel\Core\Exception;


use Throwable;

class FileNotFoundException extends \Exception
{
    public function __construct(string $message = "", int $code = ErrorCode::FILE_NOT_FOUND, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}