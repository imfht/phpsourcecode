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
use app\model\RoleAccess;
use sent\jwt\exception\JWTException;
use sent\jwt\exception\TokenExpiredException;
use sent\jwt\JWTAuth as Auth;

class ApiAuth {

	public $data = ['code' => 0];

	public function __construct(Auth $auth) {
		$this->auth = $auth;
	}

	public function handle($request, \Closure $next) {
		try {
			$auth          = $this->auth->auth();
			$user = (array) $auth['data']->getValue();
			
			$user['role'] = RoleAccess::getRoleByUid($user['uid']);
			$request->user = $user;
			//记录用户操作记录
			MemberLog::record($request);
		} catch (TokenExpiredException $e) {
			$this->data['msg']  = $e->getMessage();
			$this->data['code'] = 2001;
			return json($this->data);
		} catch (JWTException $e) {
			$this->data['code'] = 2000;
			$this->data['msg']  = $e->getMessage();
			return json($this->data);
		}

		return $next($request);
	}
}