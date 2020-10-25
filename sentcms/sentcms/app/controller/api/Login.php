<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\api;

use app\model\Member;
use think\facade\Event;
use think\Request;

/**
 * @title 登录注册
 */
class Login {

	protected $data = ['data' => [], 'code' => 0, 'msg' => ''];

	protected $middleware = [
		// \app\http\middleware\AllowCrossDomain::class,
		'\app\http\middleware\Validate',
		'\app\http\middleware\Api',
	];

	/**
	 * @title 登录
	 * @method POST
	 * @param  Member  $member  [description]
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function index(Member $member, Request $request) {
		$data = $member->login($request);

		if (false !== $data) {
			// 触发UserLogin事件 用于执行用户登录后的一系列操作
			Event::trigger('UserLogin');
			$this->data['code'] = 1;
			$this->data['msg']  = '成功登录！';
			$this->data['data'] = $data;
		} else {
			$this->data['code'] = 0;
			$this->data['msg']  = $member->error;
		}
		return $this->data;
	}

	/**
	 * @title 注册
	 * @method POST
	 * @param  Member  $member  [description]
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function register(Member $member, Request $request) {
		$data = $member->register($request);
		if (false !== $data) {
			// 触发UserRegister事件 用于执行用户注册后的一系列操作
			Event::trigger('UserRegister');
			$this->data['data'] = $data;
		} else {
			$this->data['code'] = 0;
			$this->data['msg']  = $member->error;
		}
		return $this->data;
	}
}