<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-2-11
 * Time: 18:39
 */

namespace DataComposer\Providers\file;


class xml extends _base
{
	public function get($filepath){

		if(!$this->check($filepath))return [];
		return   json_decode(json_encode(simplexml_load_file($filepath)), true);
	}

}