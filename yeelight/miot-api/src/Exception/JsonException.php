<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-12
 * Time: 下午4:15.
 */

namespace MiotApi\Exception;

use Exception;

class JsonException extends Exception
{
    private $statusCode;

    /**
     * Exception handler.
     *
     * @param string $msg    → message error (Optional)
     * @param int    $status → HTTP response status code (Optional)
     */
    public function __construct($msg = '', $status = 0)
    {
        $this->message = $msg;
        $this->statusCode = $status;
    }
}
