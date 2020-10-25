<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 22:39
 */

namespace fastwork;


use fastwork\exception\HttpRuntimeException;
use fastwork\facades\Config;
use fastwork\facades\Log;
use traits\JsonResult;

class Error
{
    use JsonResult;

    public function render(Response $response, HttpRuntimeException $e)
    {
        $response->code(404);
        self::report($e);
        if ($response->getHttpRequest()->isJson()) {
            return $this->result($e->getCode(), $e->getMessage(), $response->getHttpRequest()->id());
        } else {
            $file = Config::get('app.http_exception_template');
            if (file_exists($file)) {
                return $response->tpl(['e' => $e], $file);
            } else {
                return $this->result($e->getCode(), $e->getMessage(), $response->getHttpRequest()->id());
            }
        }
    }

    public function report(\Throwable $e)
    {
        Log::error([
            'file' => $e->getFile() . ':' . $e->getLine(),
            'msg' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTrace()
        ]);
    }
}