<?php

namespace App\Models;


class FundUserPurse extends CommonModel
{
    //
	protected $table = 'fund_user_purse';
	
	protected $casts = [
		'balance'	=> 'integer',
		'freeze'	=> 'integer',
	];
	
	public function scopeActive($query){
		return $query->where(['status'=>1]);
	}
}
