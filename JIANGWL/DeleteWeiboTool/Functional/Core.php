<?php
require_once('common.php');

/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/9/13
 * Time: 0:31
 */

/**
 * Class Core
 * 核心Functional类，功能类统一继承Core，类似于控制器的超类，主要负责初始化验证等工作
 */
class Core
{
	public $load;

	public function __construct()
	{
		//获取loader
		$this->load = Factory::getLoader();
		//设置curl的cookie
		$this->load->curl->setCookie($this->load->config->get('sina_cookie'));
	}

}