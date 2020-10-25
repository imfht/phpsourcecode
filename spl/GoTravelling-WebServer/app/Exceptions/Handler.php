<?php namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
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
      if ( $e instanceof ModelNotFoundException ) {
          return response()->json(['error' => '找不到相应的数据'], 404);
      }
        //针对 wantsJson 的情况进行处理
        if( $request->wantsJson() ){
            $resp['error'] = 'Sorry, something went wrong.';

            //在调试模式下返回更多的错误信息
            if( config('app.debug') ){
                $resp['exception'] = get_class($e);
                $resp['message'] = $e->getMessage();
                $resp['trace'] = $e->getTrace();
            }

            if( $this->isHttpException($e) ){
                $status = $e->getStatusCode();
            } else{
                $status = 500;
            }

            return response()->json($resp, $status);
        } else {
            return parent::render($request, $e);
        }

	}

}
