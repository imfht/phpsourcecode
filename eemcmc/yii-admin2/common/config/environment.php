<?php

/**
 * 环境类配置
 *
 * @author ken <vb2005xu@qq.com>
 * 
 * @property array $config 应用环境配置
 */
class Environment extends \yii\base\Object
{

	/**
	 * 环境配置
	 * @var array 
	 */
	private $_environments = [
		'online' => ['online'],
		'develop' => ['develop'],
		'ken' => ['KENXU-PC'],
	];

	/**
	 * 当前环境名称
	 * @var string
	 */
	private $_current_env = null;

	/**
	 * 应用目录路径
	 * @var type 
	 */
	private $_path = null;

	/**
	 * 构造函数
	 * @param string $path
	 */
	public function __construct($path)
	{
		$this->_path = $path;
		$this->_init();
	}

	/**
	 * 配置
	 * @var array
	 */
	private $_config = [];

	/**
	 * 初始化
	 */
	private function _init()
	{
		$servername = $this->_getServerName();
		
		foreach ($this->_environments as $key => $environment)
		{
			if (is_array($environment) && in_array($servername, $environment))
			{
				$this->_current_env = $key;
				break;
			}
			elseif ($servername == $environment)
			{
				$this->_current_env = $key;
				break;
			}
		}

		//预设环境变量
		$env = $this->_current_env;
		$env_common = $env_application = [];

		//读取app环境配置
		if ($env && file_exists($file = $this->_path . "/config/main-{$env}.php"))
		{
			$env_application = require($file);
		}
		//读取app通用配置
		$application = require($this->_path . "/config/main.php");

		//读取common环境配置
		if ($env && file_exists($file = __DIR__ . "/main-{$env}.php"))
		{
			$env_common = require($file);
		}
		//读取common通用配置
		$common = require("main.php");

		//合并配置参数
		$this->_config = $this->merge($common, $env_common, $application, $env_application);
	}

	/**
	 * 合并数组
	 * @param type $a
	 * @param type $b
	 * @return type
	 */
	private function merge($a, $b)
	{
		$args = func_get_args();
		$res = array_shift($args);
		while (!empty($args))
		{
			$next = array_shift($args);
			foreach ($next as $k => $v)
			{
				if (is_integer($k))
				{
					if (isset($res[$k]))
					{
						$res[] = $v;
					}
					else
					{
						$res[$k] = $v;
					}
				}
				elseif (is_array($v) && isset($res[$k]) && is_array($res[$k]))
				{
					$res[$k] = self::merge($res[$k], $v);
				}
				else
				{
					$res[$k] = $v;
				}
			}
		}

		return $res;
	}

	/**
	 * 获取主机名称
	 * @return string
	 */
	private function _getServerName()
	{
		$server_info = explode(' ', php_uname());
		$server_name = $server_info[0] == 'Windows' ? $server_info[2] : $server_info[1];
		if (empty($server_name))
		{
			throw new ErrorException('取不到主机名');
		}
		return $server_name;
	}

	/**
	 * 获取配置信息
	 * @return array
	 */
	public function getConfig()
	{
		return $this->_config;
	}

}
