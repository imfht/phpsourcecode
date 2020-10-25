<?php

namespace Kernel\Core\Exception;


use Throwable;

class ExecException extends \Exception
{
    public function __construct(string $message = "", int $code = ErrorCode::EXEC_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}