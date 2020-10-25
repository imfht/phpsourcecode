<?php

namespace app\merchant\controller;

use \think\Db;

class Profile
{

	public $merchant;

	public function __construct()
	{
		$this->merchant = model('Merchant')->checkLoginMerchant();
	}

	public function index()
	{
		$value = Db::name('merchant m')
			->join('account a', 'a.account_id = m.merchant_id', 'LEFT')
			->where('m.merchant_id', '=', $this->merchant['merchant_id'])
			->field('m.*, a.*')
			->find();
		include \befen\view();
	}

}

