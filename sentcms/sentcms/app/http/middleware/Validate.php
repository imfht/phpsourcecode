<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\http\middleware;
use think\Response;

class Validate {

	/**
	 * @param \think\Request $request
	 * @param \Closure $next
	 * @return mixed|\think\response\Json
	 */
	public function handle($request, \Closure $next) {
		//获取当前参数
		$params = $request->param();
		//获取访问控制器
		if(\strripos($request->controller(), ".")){
			$controller = explode(".", $request->controller());
		}else{
			$controller = ['', ucfirst($request->controller())];
		}

		//获取操作名,用于验证场景scene
		$scene = strtolower($controller[0]) . $request->action();
		$validate = "app\\http\\validate\\" . ucfirst($controller[1]);
		//仅当验证器存在时 进行校验
		if (class_exists($validate) && $request->isPost()) {
			$v = new $validate;
			//仅当存在验证场景才校验
			if ($v->hasScene($scene)) {
				//设置当前验证场景
				$v->scene($scene);
				if (!$v->check($params)) {
					//校验不通过则直接返回错误信息
					$data = array(
						'msg' => $v->getError(),
						'code' => 0,
						'data' => '',
						'time' => time(),
					);
					return Response::create($data, 'json', 200);
				}
			}
		}
		return $next($request);
	}
}