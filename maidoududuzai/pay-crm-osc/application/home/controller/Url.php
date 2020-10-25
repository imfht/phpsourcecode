<?php

namespace app\home\controller;

use \think\Db;

class Url
{

	public function __construct()
	{
		
	}

	public function index($str = null)
	{
		$value = model('Url')->get_one(['str' => $str]);
		if(!empty($value['url'])) {
			return \befen\redirect($value['url']);
		}
	}

	public function create($url = null)
	{
		if(empty($url)) {
			$url = input('param.url');
		}
		return JSON(model('Url')->make($url));
	}

}

