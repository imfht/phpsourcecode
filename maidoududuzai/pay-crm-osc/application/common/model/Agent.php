<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class Agent extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	public static $list_status = [1 => '正常', 0 => '关闭'];

	public static function getStatus($status = null)
	{
		if(!isset(self::$list_status[$status])) {
			if(is_null($status)) {
				return self::$list_status;
			} else {
				return $status;
			}
		} else {
			return self::$list_status[$status];
		}
	}

	public static function getLoginAgent()
	{
		$login = Session::get('agent');
		if(empty($login['agent_id'])) {
			return [];
		} else {
			$value = self::get_one($login['agent_id']);
			if(!$value || $value['password'] != $login['password']) {
				return [];
			} else {
				return $value;
			}
		}
	}

	public static function checkLoginAgent()
	{
		$login = self::getLoginAgent();
		if($login) {
			return $login;
		} else {
			if(request()->module() != 'agent') {
				return \befen\redirect('/mo/login/index');
			} else {
				return \befen\error('登录超时、请重新登录', 'agent/login/index');
			}
		}
	}

	public static $list_type = [
		0 => '个人',
		1 => '企业',
	];

	public static function get_type($type = null)
	{
		if(!isset(self::$list_type[$type])) {
			if(is_null($type)) {
				return self::$list_type;
			} else {
				return $type;
			}
		} else {
			return self::$list_type[$type];
		}
	}

}

