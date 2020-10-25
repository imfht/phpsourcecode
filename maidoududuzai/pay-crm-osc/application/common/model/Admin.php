<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class Admin extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	public static $list_status = [0 => '关闭', 1 => '正常'];

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

	public static function getLoginAdmin()
	{
		$login = Session::get('admin');
		if(empty($login['id'])) {
			return [];
		} else {
			$value = self::get_one($login['id']);
			if(!$value || $value['password'] != $login['password']) {
				return [];
			} else {
				return $value;
			}
		}
	}

	public static function checkLoginAdmin()
	{
		$login = self::getLoginAdmin();
		if($login) {
			return $login;
		} else {
			return \befen\error('登录超时、请重新登录', 'console/login/index');
		}
	}

}

