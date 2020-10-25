<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\api;

use think\App;

class Base {
	/**
	 * Request实例
	 * @var \think\Request
	 */
	protected $request;

	/**
	 * 应用实例
	 * @var \think\App
	 */
	protected $app;

	public $middleware = [
		'\app\http\middleware\Validate',
		// 'sent\jwt\middleware\JWTAuth' => ['except' => ['login']],
		'\app\http\middleware\ApiAuth',
		'\app\http\middleware\Api',
		// '\app\http\middleware\AllowCrossDomain',
	];

	protected $data = ['data' => [], 'code' => 0, 'msg' => ''];

	/**
	 * 构造方法
	 * @access public
	 * @param  App  $app  应用对象
	 */
	public function __construct(App $app) {
		$this->app = $app;
		$this->request = $this->app->request;
		// 控制器初始化
		$this->initialize();
	}

	// 初始化
	protected function initialize() {}

	protected function success($msg, $url = '') {
		$this->data['code'] = 1;
		$this->data['msg']  = $msg;
		$this->data['url']  = $url ? $url->__toString() : '';
		return $this->data;
	}

	protected function error($msg, $url = '') {
		$this->data['code'] = 0;
		$this->data['msg']  = $msg;
		$this->data['url']  = $url ? $url->__toString() : '';
		return $this->data;
	}
}