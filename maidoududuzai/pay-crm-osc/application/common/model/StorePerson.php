<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class StorePerson extends Model
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

	public static function getLoginPerson()
	{
		$login = Cookie::get('person');
		if(empty($login['person_id'])) {
			return [];
		} else {
			$value = self::get_one($login['person_id']);
			if(!$value || $value['password'] != $login['password']) {
				return [];
			} else {
				return $value;
			}
		}
	}

	public static function checkLoginPerson()
	{
		$login = self::getLoginPerson();
		if($login) {
			return $login;
		} else {
			return \befen\redirect('/mp/login/index');
		}
	}

	public static $list_manager = [0 => '店员', 1 => '店长'];

	public static function getManager($manager = null)
	{
		if(!isset(self::$list_manager[$manager])) {
			if(is_null($manager)) {
				return self::$list_manager;
			} else {
				return $manager;
			}
		} else {
			return self::$list_manager[$manager];
		}
	}

}

