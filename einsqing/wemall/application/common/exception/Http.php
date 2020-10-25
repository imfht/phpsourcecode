<?php
namespace app\common\exception;

use Exception;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;
use Raven_Client;
use Raven_ErrorHandler;
class Http extends Handle
{
	protected $client;
    public function render(Exception $e)
    {
    	try{
            $sentryClient = new Raven_Client(config('sentry.dsn'),[
	        	'release' => config('sentry.version')
	        ]);
            $error_handler = new Raven_ErrorHandler($sentryClient);
            $error_handler->registerExceptionHandler();
            $error_handler->registerErrorHandler();
            $error_handler->registerShutdownFunction();
            $this->client = $sentryClient;
        }catch (Exception $e){
            throw new Exception('Client Keys 配置不正确');
        }
        // $this->client->captureException($e,[
        // 	'release' => config('sentry.version')
        // ]);

        // 参数验证错误
        if ($e instanceof ValidateException) {
            return json($e->getError(), 422);
        }

        // 请求异常
        if ($e instanceof HttpException && request()->isAjax()) {
            return response($e->getMessage(), $e->getStatusCode());
        }


        // 其他错误交给系统处理
        return parent::render($e);
    }

}