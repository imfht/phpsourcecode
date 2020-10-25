<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class Config extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	// method for one
	public static function one($field = null, $_update = null)
	{
		$_config = config('_config');
		if(empty($_config) || !empty($_update)) {
			$_config = self::get_all();
			config('_config', $_config);
		}
		if(empty($_config)) {
			return [];
		}
		foreach($_config as $key => $val) {
			if($field == $val['key']) {
				return $val;
			}
		}
		return [];
	}

	// method for list
	public static function list($prefix = null, $_update = null)
	{
		$_config = config('_config');
		if(empty($_config) || !empty($_update)) {
			$_config = self::get_all();
			config('_config', $_config);
		}
		if(empty($_config)) {
			return [];
		}
		$list = [];
		foreach($_config as $key => $val) {
			if(empty($prefix)) {
				$list[$val['key']] = $val;
			} else {
				if(preg_match('/^' . preg_quote($prefix, '/') . '/', $val['key'])) {
					$list[$val['key']] = $val;
				}
			}
		}
		return $list;
	}

	// method for value of config
	public static function config($field = null, $prefix = null, $_update = null)
	{
		$_config = self::list($prefix, $_update);
		if(empty($_config)) {
			if(empty($field)) {
				return [];
			} else {
				return null;
			}
		}
		if(!empty($field)) {
			foreach($_config as $key => $val) {
				if($key == $field) {
					return $val['value'];
				}
			}
			return null;
		}
		$list = [];
		foreach($_config as $key => $val) {
			if(empty($prefix)) {
				$list[$val['key']] = $val['value'];
			} else {
				if(preg_match('/^' . preg_quote($prefix, '/') . '/', $val['key'])) {
					$list[$val['key']] = $val['value'];
				}
			}
		}
		return $list;
	}

}

