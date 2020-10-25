<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/5
 * Time: 19:35
 */

namespace fastwork\exception;


use fastwork\facades\Log;

class DbException extends \Exception
{
    public function __construct($message = "", $code = 0, $previous = null)
    {
        Log::sql('[Db] : ' . $message);
        parent::__construct($message, $code, $previous);
    }

}