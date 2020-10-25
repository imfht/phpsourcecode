<?php

namespace App\Exceptions;

use Exception, Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyDisplayer;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        return parent::render($request, $e);
    }

    /**
     * 自定义错误页
     */
    protected function renderHttpException(HttpException $e)
    {
        if(Request::ajax() and ! config('app.debug'))
        {
            return response()->json(['error_code' => $e->getStatusCode()]);
        }
        elseif (view()->exists('error.common') and ! config('app.debug'))
        {
            return response()->view('error.common', ['errorCode' => $e->getStatusCode()], $e->getStatusCode());
        }
        else
        {
            return (new SymfonyDisplayer(config('app.debug')))->createResponse($e);
        }
    }

}
