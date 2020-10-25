<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Common\Api;
define('UC_CLIENT_PATH', dirname(dirname(__FILE__)));

//载入配置文件
require_cache(UC_CLIENT_PATH . '/Conf/config.php');

//载入函数库文件
require_cache(UC_CLIENT_PATH . '/Common/common.php');

/**
 * UC API调用控制器层
 * 调用方法 A('Uc/User', 'Api')->login($username, $password, $type);
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
		$this->_init();
	}

	/**
	 * 抽象方法，用于设置模型实例
	 */
	abstract protected function _init();

}
