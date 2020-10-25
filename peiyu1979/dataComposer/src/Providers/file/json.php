<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-2-11
 * Time: 18:12
 */

namespace DataComposer\Providers\file;


class json extends _base
{
	public function get($filepath){

		if(!$this->check($filepath))return [];
		return json_decode(file_get_contents($filepath),true) ;
	}

}