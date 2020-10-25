<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        if (!$exception instanceof ApiException) {
            parent::report($exception);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        //接管api的错误提示
        if ($exception instanceof ApiException) {
            $error_info = $exception->getMessage();
            $return = array(
                'code' => '10000',
                'status_code' => '200',
                'msg' => $error_info
            );
            if (strpos($error_info, '|')) {
                $error_info = explode('|', $error_info);
                $return['code'] = $error_info[0];
                $return['msg'] = $error_info[1];
            }
            return response()->json($return, 200, array(), JSON_UNESCAPED_UNICODE);
        }
        //接管500错误
        $statusCode = 500;
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        } elseif ($exception instanceof RequestExceptionInterface) {
            $statusCode = 400;
        }
        $debug = config('app.debug');
        //自定义500错误
        if ($statusCode == 500 && $debug) {
            $return = array(
                'code' => '10000',
                'status_code' => '500',
                'msg' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            );
            return response()->json($return, 200, array('Access-Control-Allow-Origin' => '*'), JSON_UNESCAPED_UNICODE);
        } elseif ($statusCode == 429) {
            $return = array(
                'code' => '10000',
                'status_code' => '429',
                'msg' => '服务器太忙了，请重试'
            );
            return response()->json($return, 200, array('Access-Control-Allow-Origin' => '*'), JSON_UNESCAPED_UNICODE);
        }
        return parent::render($request, $exception);
    }
}
