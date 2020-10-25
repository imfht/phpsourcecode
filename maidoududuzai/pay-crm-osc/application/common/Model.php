<?php

namespace app\common;

class Model extends \think\Model
{

	// Basic Model Extends

	//protected $pk = 'id';
	//protected $resultSetType = 'collection';

	public static function get_one($data, $with = [], $cache = false)
	{
		$res = self::get($data, $with, $cache);
		if(empty($res)) {
			return [];
		} else {
			return $res->toArray();
		}
	}

	public static function get_all($data = null, $with = [], $cache = false)
	{
		$res = self::all($data, $with, $cache);
		if(empty($res)) {
			return [];
		} else {
			return $res->toArray();
		}
	}

}

