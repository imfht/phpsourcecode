<?php

namespace Freyo\Xinge\Exceptions;

use Freyo\Xinge\Exception as BaseException;

class CouldNotSendNotification extends BaseException
{
    /**
     * @param string $message
     * @param int    $code
     *
     * @return static
     */
    public static function serviceRespondedWithAnError($message = '', $code = 0)
    {
        return new static(
            "Xinge responded with an error '{$message}: {$code}'", $code
        );
    }
}
