<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class MerchantWeixin extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	//状态
	public static $list_status = [-1 => '-', 0 => '关闭', 1 => '正常'];

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

}

