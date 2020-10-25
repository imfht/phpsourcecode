<?php namespace qeephp\tools;

use qeephp\Halt;

class Profiler
{
	
	CONST CPU = 1;
	CONST MEMORY = 2;
	CONST NO_BUILTINS = 3;	
	CONST CPU_MEMORY = 4;
	CONST NO_BUILTINS_CPU = 5;
	CONST NO_BUILTINS_MEMORY = 6;
	CONST NO_BUILTINS_CPU_MEMORY = 7;
	
	private $saver = null;
	
	private function __construct(array $saver)
	{
		if (!empty($saver['class']))
		{
			$class = trim($saver['class']);
			$params = (array) val($saver, 'config', NULL);
			if ( !empty($class) )
			{
				$this->saver = new $class($params);
			}
		}
		Halt::getInstance()->add( array($this, '__flush') );
	}
		
	static function execute(array $options)
	{
		static $do = false;
		if ($do) return;
		$do = TRUE;
		
		if (!empty($options['enable']))
		{
			$k = trim($options['url_param']);
			$v = trim($options['url_val']);
			if(!empty($k) && !empty($v) && !empty($_REQUEST[ $k ]) && ($_REQUEST[ $k ] === $v))
			{
				if (!function_exists('xhprof_enable'))
				{
					return trigger_error('xhprof not install',E_USER_WARNING);
				}
				
				$level = intval($options['level']);
				$args = array('ignored_functions' => array('call_user_func',
                                                  'call_user_func_array'));
				switch ($level)
				{
					case self::CPU:
						$level = XHPROF_FLAGS_CPU;
						break;
					case self::MEMORY:
						$level = XHPROF_FLAGS_MEMORY;
						break;					
					case self::NO_BUILTINS:
						$level = XHPROF_FLAGS_NO_BUILTINS;
						break;
					case self::CPU_MEMORY:
						$level = XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY;
						break;
					case self::NO_BUILTINS_CPU:
						$level = XHPROF_FLAGS_CPU + XHPROF_FLAGS_NO_BUILTINS;
						break;
					case self::NO_BUILTINS_MEMORY:
						$level = XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS;
						break;
					default:
						$level = XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS;
						break;	
				}
				
				xhprof_enable($level, $args);
				new self(val($options, 'saver',array()));
			}
		}
		
	}
	
	function __flush()
	{
		$data = xhprof_disable();
		
		if (!empty($this->saver))
		{
			$this->saver->save($data);			
		}
		else
		{
			dump($data,'xhprof');
		}
	}
}