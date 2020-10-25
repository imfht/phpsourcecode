<?php
namespace Db\Api;
define('DB_CLIENT_PATH', dirname(dirname(__FILE__)));

//载入配置文件
require_cache(DB_CLIENT_PATH . '/Conf/config.php');

//载入函数库文件
require_cache(DB_CLIENT_PATH . '/Common/common.php');

/**
 * UC API调用控制器层
 * 调用方法 A('Db/User', 'Api')->login($username, $password, $type);
 */
abstract class Api{

	/**
	 * API调用模型实例
	 * @access  protected
	 * @var object
	 */
	protected $model;

	/**
	 * 构造方法，检测相关配置
	 */
	public function __construct(){
		//相关配置检测
		defined('DB_APP_ID') || throw_exception('UC配置错误：缺少DB_APP_ID');
		defined('DB_API_TYPE') || throw_exception('UC配置错误：缺少DB_APP_API_TYPE');
		defined('DB_AUTH_KEY') || throw_exception('UC配置错误：缺少DB_APP_AUTH_KEY');
		defined('DB_DB_DSN') || throw_exception('UC配置错误：缺少DB_DB_DSN');
		defined('DB_TABLE_PREFIX') || throw_exception('UC配置错误：缺少DB_TABLE_PREFIX');
		if(DB_API_TYPE != 'Model' && DB_API_TYPE != 'Service'){
			throw_exception('UC配置错误：DB_API_TYPE只能为 Model 或 Service');
		}
		if(DB_API_TYPE == 'Service' && DB_AUTH_KEY == ''){
			throw_exception('UC配置错误：Service方式调用Api时DB_AUTH_KEY不能为空');
		}
		if(DB_API_TYPE == 'Model' && DB_DB_DSN == ''){
			throw_exception('UC配置错误：Model方式调用Api时DB_DB_DSN不能为空');
		}

		$this->_init();
	}

	/**
	 * 抽象方法，用于设置模型实例
	 */
	abstract protected function _init();
}
