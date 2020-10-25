<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-2-11
 * Time: 18:28
 */

namespace DataComposer\Providers\file;


class phparray extends _base
{
	public function get($filepath){

		if(!$this->check($filepath))return [];
		return require($filepath) ;
	}

}