<?php

namespace Cheer\TpTrace;

use Illuminate\Support\ServiceProvider;
use DB;
use Log;
class TpTraceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    	$GLOBALS['_startUseMems'] = memory_get_usage();
    	$GLOBALS['_querySql'] = [];
    	$GLOBALS['_debugInfo'] = [];
    	$fun_path = __DIR__.DIRECTORY_SEPARATOR.'function.php';
    	$view_path = __DIR__.DIRECTORY_SEPARATOR.'views';
    	$config_path = __DIR__.DIRECTORY_SEPARATOR.'config.php';
    	require $fun_path;
    	
    	//加载视图
    	$this->loadViewsFrom($view_path, 'traceview');
    	//发布配置
    	$this->publishes([
    			$config_path => config_path('thinkphp_trace.php'),
    	]);
    	
    	DB::listen(function($sql, $bindings, $time){
    		$GLOBALS['_querySql'][] =  ['sql'=>$sql, 'bindings'=>$bindings, 'time'=>$time];
    	});
    	Log::listen(function($level, $message) {
    		$GLOBALS['_debugInfo'][] = ['level'=>$level, 'message'=>$message];
    	});
    	

    	//程序加载完毕后执行
    	register_shutdown_function([$this,'pageTraceRun']);
     
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    	$config_path = __DIR__.DIRECTORY_SEPARATOR.'config.php';
    	$this->mergeConfigFrom(
    			$config_path, 'thinkphp_trace'
    	);
    	
    }
    
    /*
     * 调用trace
     */
    public function pageTraceRun(){
    	$traceRes = new ShowPageTrace();
    	$traceRes->show();
    }
    
}
