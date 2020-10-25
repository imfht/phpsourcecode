<?php

namespace app\mock\controller;

use \think\exception\HttpException;

//接口网络异常状态模拟
class Index
{
	public $status_map = ['200', '200/0', '200/text', '500/json', '500/text', 'timeout'];

	/**
	 * 中间件
	 * @param string $r 原路径，例：/mock?r=pay/cash/login
	 * @return mixed
	 */
	public function index(){
		//获取真实路由
		$route = request()->route();
		$module = $route['module'];
		$controller = $route['controller'];
		$action = $route['action'];
		//原控制器类
		$class = "\app\\$module\controller\\".ucfirst($controller);
		//获取路由mock配置
		$mockcfg = \json_decode(\file_get_contents( __DIR__.DS.'..'.DS.'mockcfg.php'), true);
		try {
			$status = $mockcfg['config'][$module][$controller][$action];
			$status = $this->status_map[$status];
		} catch (\Throwable $th) {
			$status = '200';
		}
		if($mockcfg['active'] == '0' || $status == '200'){
			return call_user_func(array(new $class, $action));
		}
		return $this->mock_return($status);
	}

	public function mock_return($status)
	{
		switch ($status) {
			case '200/0':
				$res = \make_json(0, 'status ' . $status . '(测试)');
				break;
			case '200/text':
				$res = 'status ' . $status . '(测试)';
				break;
			case '500/json':
				throw new HttpException(500, 'status ' . $status . '(测试)');
			break;
			case '500/text':
				echo 'status ' . $status . '(测试)';
				$a.test;
				break;
			case 'timeout':
				sleep(16);
				$res = '';
				break;
		}
		return $res;
	}
}