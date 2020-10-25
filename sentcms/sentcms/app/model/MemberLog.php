<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\model;

use think\Model;
use xin\helper\Server;

/**
 * @title: 用户日志模型
 */
class MemberLog extends Model {

	protected $type = [
		'param' => 'json',
		'visite_time' => 'timestamp',
	];

	public static function record($request) {
		$data = [
			'uid' => $request->user['uid'],
			'title' => self::getCurrentTitle($request),
			'url' => $request->baseUrl(),
			'param' => $request->param(),
			'method' => $request->method(),
			'visite_time' => $request->time(),
			'client_ip' => Server::getRemoteIp(),
			'create_time' => time(),
		];
		self::create($data);
	}

	public function getMemberLogList($request) {
		$param = $request->param();
		$map = [];
		$order = "id desc";

		return self::with(['user'])->where($map)->order($order)->paginate($request->pageConfig);
	}

	public function user() {
		return $this->hasOne('Member', 'uid', 'uid')->field('uid,nickname,username');
	}

	protected static function getCurrentTitle($request) {
		$mate = '';
		$controller = strtr(strtolower($request->controller()), '.', '\\');
		$action = $request->action();
		$class = "\\app\\controller\\" . $controller;
		if (class_exists($class)) {
			$reflection = new \ReflectionClass($class);
			$group_doc = self::Parser($reflection->getDocComment());
			if (isset($group_doc['title'])) {
				$mate = $group_doc['title'];
			}
			$method = $reflection->getMethods(\ReflectionMethod::IS_FINAL | \ReflectionMethod::IS_PUBLIC);
			foreach ($method as $key => $v) {
				if ($action == $v->name) {
					$title_doc = self::Parser($v->getDocComment());
					if (isset($title_doc['title'])) {
						$mate = $title_doc['title'];
					}
				}
			}
		}
		return $mate;
	}

	protected static function Parser($text) {
		$doc = new \doc\Doc();
		return $doc->parse($text);
	}
}