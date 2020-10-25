<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class Url extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	public static function make($url = null)
	{
		if(empty($url)) {
			$url = input('param.url');
		}
		if(!preg_match('/^https?:\/\//i', $url)) {
			return make_return(0, 'Invalid param [url]');
		}
		do {
			$str = get_rand(8);
		} while(0 != Db::name('url')->where('str', '=', $str)->count());
		$data = [
			'str' => $str,
			'url' => $url,
		];
		$value = Db::name('url')->where('url', '=', $url)->find();
		if(empty($value)) {
			Db::name('url')->insert($data);
			$contents = [
				'str' => $str,
				'url' => url('/t/' . $str, null, null, true),
			];
		} else {
			$contents = [
				'str' => $value['str'],
				'url' => url('/t/' . $value['str'], null, null, true),
			];
		}
		return make_return(1, 'ok', $contents);
	}

}

