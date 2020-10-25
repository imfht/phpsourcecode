<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class Merchant extends Model
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

	public static function getLoginMerchant()
	{
		$login = Session::get('merchant');
		if(empty($login['merchant_id'])) {
			return [];
		} else {
			$value = self::get_one($login['merchant_id']);
			if(!$value || $value['password'] != $login['password']) {
				return [];
			} else {
				return $value;
			}
		}
	}

	public static function checkLoginMerchant()
	{
		$login = self::getLoginMerchant();
		if($login) {
			return $login;
		} else {
			if(request()->module() != 'merchant') {
				return \befen\redirect('/mp/login/index');
			} else {
				return \befen\error('登录超时、请重新登录', 'merchant/login/index');
			}
		}
	}

	public static $list_check_status = [
		-1 => '结束',
		0 => '未申请',
		1 => '审核中',
		2 => '完善资料',
	];

	public static function getCheckStatus($status = null)
	{
		if(!isset(self::$list_check_status[$status])) {
			if(is_null($status)) {
				return self::$list_check_status;
			} else {
				return $status;
			}
		} else {
			return self::$list_check_status[$status];
		}
	}

	public static $list_type = [
		//'0' => '个人',
		'SUBJECT_TYPE_ENTERPRISE' => '企业',
		'SUBJECT_TYPE_INDIVIDUAL' => '个体户',
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

	public static $list_industry = [
		1 => '餐饮',
		2 => '零售',
		3 => '超市',
		999 => '其他',
	];

	public static function get_industry($industry = null)
	{
		if(!isset(self::$list_industry[$industry])) {
			if(is_null($industry)) {
				return self::$list_industry;
			} else {
				return $industry;
			}
		} else {
			return self::$list_industry[$industry];
		}
	}

}

