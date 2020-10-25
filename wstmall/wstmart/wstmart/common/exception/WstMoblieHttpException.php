<?php
namespace wstmart\common\exception;
use think\exception\Handle;
// 手机版异常处理类
class WstMoblieHttpException extends Handle
{

    public function render(\Exception $e)
    {
    	if(config('app_debug')){
    		return parent::render($e);
    	}else{
    	    header("Location:".url('mobile/error/index'));
    	}
    }

}