<?php
namespace WebService\Controller;
use Think\Controller\RestController;

/**
 * Rest服务类控制器的基类控制器
 */
class BaserestController extends RestController
{
	/**
	 * 处理未被路由的WebService模块下的Rest请求
	 */
	Public function rest()
	{
		exit('非法地址Rest请求');
	}
}