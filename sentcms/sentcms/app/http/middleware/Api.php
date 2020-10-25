<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\http\middleware;
use app\model\Config;
use app\model\Department;
use app\model\Dictionary;
use app\model\Firm;
use think\facade\Cache;

class Api {

	protected $data = [];

	public function handle($request, \Closure $next) {
		$request->pageConfig = array(
			'list_rows' => $request->param('limit', 30),
			'page'      => $request->param('page', 1),            
		);
		$this->cacheData($request); //缓存基础数据
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

	public function cacheData($request) {
		//缓存配置信息
		$config = Cache::get('config');
		if (!$config) {
			Cache::set('config', Config::getConfigList($request));
		}
	}

}