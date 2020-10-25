<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\http\middleware;

use app\model\MemberLog;
use think\facade\Session;

/**
 * @title 后台中间件
 */
class Admin {

	public $data = [];

	public function handle($request, \Closure $next) {
		$request->rootUid = env('rootuid');
		$request->user = Session::get('adminInfo');
		$request->url = str_replace(".", "/", strtolower($request->controller())) . '/' . $request->action();

		$request->pageConfig = array(
			'list_rows' => $request->param('limit', 20),
			'page' => $request->param('page', 1),
			'query' => $request->param(),
		);
		MemberLog::record($request);

		$response = $next($request);
		if (is_array($response->getData())) {
			$this->data = array_merge($this->data, $response->getData());
		} else {
			$this->data = $response->getData();
		}

		if ($request->isAjax()) {
			return json($this->data);
		} else {
			if (\is_string($this->data) && $this->data != '') {
				return $response;
			} else {
				return json($this->data);
			}
		}
	}
}