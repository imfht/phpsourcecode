<?php namespace qeephp\error;

use qeephp\Config;
use qeephp\Halt;
use qeephp\tools\FuelCli;

class Handler
{

    /**
     * 使用内置的错误处理器
     */
    static function init()
    {
        $config = (array) Config::get('app.error');

        # 设置错误^异常处理函数
        error_reporting(val($config, 'level', E_ALL | E_STRICT));
        ini_set('display_errors', 0);

        if ( !empty($config['fatal']) && is_callable($config['fatal']) )
        {
            Halt::getInstance()->add( $config['fatal'] );
        }
        if ( !empty($config['userlevel']) && is_callable($config['userlevel']) )
        {
            restore_error_handler();
            set_error_handler($config['userlevel']);
        }
        if ( !empty($config['exception']) && is_callable($config['exception']) )
        {
            ini_set('display_errors', 0);
            restore_exception_handler();
            set_exception_handler( $config['exception'] );
        }

    }

	private static function formatErrno($errno)
	{
		static $codes = array(
			E_ERROR => 'Fatal run-time errors',
			E_RECOVERABLE_ERROR => 'Catchable fatal error',
			E_WARNING => 'Run-time warnings',
			E_PARSE => 'Compile-time parse errors',
			E_NOTICE => 'Run-time notices',
			E_STRICT => 'E_STRICT',
			E_CORE_ERROR => 'Fatal errors that occur during PHP initial startup',
			E_CORE_WARNING => 'Warnings that occur during PHP initial startup',
			E_COMPILE_ERROR => 'Fatal compile-time errors',
			E_COMPILE_WARNING => 'Compile-time warnings',
			E_USER_ERROR => 'User-generated error',
			E_USER_WARNING => 'User-generated warning',
			E_USER_NOTICE => 'User-generated notice',
		);
		return val($codes, $errno, 'Unknown error type');
	}
	
	/**
	 * 框架自带的异常处理(供借鉴)
	 * 
	 * @param \Exception $ex
	 */
	static function exception(\Exception $ex)
	{
        if ( APP_IN_CLI ) return self::consoleException($ex);

		$viewDir = __DIR__ . "/_views";

		$code = $ex->getCode();
		$template = "{$viewDir}/code/{$code}.php";
		if ( !is_readable($template) )
		{
			$template = "{$viewDir}/exception.php";
		}
		include($template);
	    exit;
	}
	
	/**
	 * 框架自带的系统级错误处理(供借鉴)
	 * 
	 * @param array $error
	 */
	static function fatal(array $error =null)
	{
        if ( APP_IN_CLI ) return self::consoleFatal($error);

		if (empty($error) || !is_array($error))
		{
			$error = error_get_last();	
		} 
		
		if (!empty($error))
		{
			$viewDir = __DIR__ . "/_views";
			
			$title = self::formatErrno($error['type']);
			$errfile = $error['file'];
			
			$template = "{$viewDir}/error.php";
			include($template);
			
			exit;
		}
	}
	
	/**
	 * 框架自带的用户级错误处理(供借鉴)
	 */
	static function userlevel($errno, $errstr, $errfile, $errline, $errcontext)
	{
        if ( APP_IN_CLI ) return self::consoleUserlevel($errno, $errstr, $errfile, $errline, $errcontext);
		if (!(error_reporting() & $errno)) {
	        // This error code is not included in error_reporting
	        return;
	    }
	    
	    if ( E_USER_ERROR == $errno )
	    {
	    	# 用户触发的错误,并不能使用 error_get_last 获取
	    	# 可以使用 trigger_error 来触发用户级别错误
	    	$error = array(
	    		'type' => $errno,
	    		'message' => $errstr,
	    		'file' => $errfile,
	    		'line' => $errline,
	    	);
	    	self::fatal($error);
	    }
	    else
	    {
            $viewDir = __DIR__ . "/_views";
	    	$title = self::formatErrno($errno);
	    	echo "<b>{$title}: </b> {$errstr} on File {$errfile}:{$errline}<br />\n";	    
	    }

	    /* Don't execute PHP internal error handler */
	    return true;
	}
	
	### "终端控制台"下的 错误&异常处理
	static function consoleException(\Exception $ex)
	{
		$file = $ex->getFile();
		$line = $ex->getLine();
		$code = $ex->getCode();
		$msg = $ex->getMessage();
        $exclass = get_class($ex);
		
		FuelCli::write("[异常] {$exclass} (code={$code})",'red');
		FuelCli::write("[描述] {$msg}",'yellow');
		FuelCli::write("[{$line}] :{$file}");
		
		# 读取相应源代码行
		$prev = 7; $next = 7;
		$data = file($ex->getFile());
	    $count = count($data) - 1;
	
	    //count which lines to display
	    $start = $line - $prev;
	    if ($start < 1) {
	        $start = 1;
	    }
	    $end = $line + $next;
	    if ($end > $count) {
	        $end = $count + 1;
	    }
	
	    //displaying
	    $out = '';	
	    for ($x = $start; $x <= $end; $x++) {
	        
	        $out .= $line != "{$x}    " ? $x : str_repeat('-',strlen($x));
	
	        $out .= "    ";
	        $out .= $data[$x - 1];
	    }
	    FuelCli::write($out,'light_red');
		
		# 打印堆栈		
		$trace = $ex->getTrace();
		foreach ($trace as $point) {
			
	        $file = isset($point['file']) ? $point['file'] : null;
	        $line = isset($point['line']) ? $point['line'] : null;
	        
	        $function = isset($point['class']) ? "{$point['class']}::{$point['function']}" : $point['function'];
	
	        $args = array();$dump_args = false;
	        
	        if (is_array($point['args']) && count($point['args']) > 0) {
	            foreach ($point['args'] as $arg) {
	                switch (gettype($arg)) {
	                case 'array':
	                    $args[] = 'array(' . count($arg) . ')';
	                    $dump_args = TRUE;
	                    break;
	                case 'resource':
	                    $args[] = gettype($arg);
	                    break;
	                case 'object':
	                    $args[] = get_class($arg);
	                    $dump_args = TRUE;
	                    break;
	                case 'string':
	                    if (strlen($arg) > 30) {
	                        $arg = substr($arg, 0, 27) . ' ...';
	                    }
	                    $args[] = "'{$arg}'";
	                    break;
	                default:
	                    $args[] = $arg;
	                }
	            }
	        }
	        
	        $args = implode(", ", $args);

	        FuelCli::write("[{$line}] :{$file}");
			FuelCli::write("{$function}($args)",'light_red');
			if ($dump_args)
			{
				FuelCli::write("[参数] " . print_r($point['args'],true) ,'light_red');
			}			
		}
		
	    exit;
	}
		
	static function consoleFatal(array $error =null)
	{		
		if (empty($error) || !is_array($error))
		{
			$error = error_get_last();	
		} 
		
		if (!empty($error))
		{			
			FuelCli::write("[错误] " . self::formatErrno($error['type']) . " `{$error['message']}` ",'light_red');
			FuelCli::write("[{$error['line']}] :{$error['file']}");
		}
		exit;
	}
		
	static function consoleUserlevel($errno, $errstr, $errfile, $errline, $errcontext)
	{		
		if (!(error_reporting() & $errno)) {
	        // This error code is not included in error_reporting
	        return;
	    }
	    
	    if ( E_USER_ERROR == $errno )
	    {
	    	# 用户触发的错误,并不能使用 error_get_last 获取
	    	# 可以使用 trigger_error 来触发用户级别错误
	    	$error = array(
	    		'type' => $errno,
	    		'message' => $errstr,
	    		'file' => $errfile,
	    		'line' => $errline,
	    	);
	    	self::consoleFatal($error);
	    }
	    else
	    {
	    	FuelCli::write("[提示] " . self::formatErrno($errno) . " `{$errstr}` ",'yellow');
	    	FuelCli::write("[{$errline}] :{$errfile}");
	    }

	    /* Don't execute PHP internal error handler */
	    return true;
	}
	
}