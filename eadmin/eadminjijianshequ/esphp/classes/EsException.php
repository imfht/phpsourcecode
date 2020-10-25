<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace esclass;

class EsException extends \Exception
{
    /**
     * 注册异常处理
     *
     * @return void
     */
    public static function register()
    {
        error_reporting(0);  //关闭错误输出

        set_error_handler([__CLASS__, 'appError']);
        set_exception_handler([__CLASS__, 'appException']);
        register_shutdown_function([__CLASS__, 'appShutdown']);
    }

    /**
     * Exception Handler  异常处理
     *
     * @param
     */
    public static function appException($e)
    {
        if (webconfig('app_debug') == 1) {
            self::showError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
        } else {
            echo $str = '
			<div class="echo">
			</div>
			<div class="exception">
			
			<div class="info"><h1>页面错误！请稍后再试～</h1></div>
			
			</div>
			
			
			
			<div class="copyright">
			<a title="官方网站" href="http://www.imzaker.com">ESPHP</a>
			<span>V1.0.0</span>
					<span>{ 为懒惰而生的PHP开发框架 }</span>
				
					</div>
			
			
				';

        }

    }

    /**
     * Error Handler  错误处理
     *
     * @param  integer $errno   错误编号
     * @param  integer $errstr  详细错误信息
     * @param  string  $errfile 出错的文件
     * @param  integer $errline 出错行号
     * @param array    $errcontext
     * @throws ErrorException
     */
    public static function appError($errno, $errstr, $errfile = '', $errline = 0, $errcontext = [])
    {
        //self::showError( $errno , $errstr , $errfile , $errline );
    }

    /**
     * Shutdown Handler 捕获致命错误
     */
    public static function appShutdown()
    {
        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])) {
            self::showError(0, $error["message"], $error["file"], $error["line"]);
        }
    }

    /**
     * 确定错误类型是否致命
     *
     * @param  int $type
     * @return bool
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    /**
     * @param        $errno   错误号
     * @param        $errstr  错误提示信息
     * @param string $errfile 错误文件
     * @param int    $errline 错误行号
     */
    public static function showError($errno, $errstr, $errfile = '', $errline = 0)
    {
        echo "ERROR : {$errstr} ; file : {$errfile} ; line : {$errline} <br>";
    }
}