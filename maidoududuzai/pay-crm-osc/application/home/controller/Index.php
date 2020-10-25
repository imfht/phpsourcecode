<?php

namespace app\home\controller;

use \think\Db;

class Index
{

	public function __construct()
	{

	}

	public function index()
	{

		$admin = model('Admin')->getLoginAdmin();
		$agent = model('Agent')->getLoginAgent();
		$merchant = model('Merchant')->getLoginMerchant();
		include \befen\view();

	}


}

