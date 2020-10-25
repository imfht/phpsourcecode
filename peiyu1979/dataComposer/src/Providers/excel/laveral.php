<?php

namespace DataComposer\Providers\excel;

use DataComposer\comm;
use Excel;

/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-4
 * Time: 13:22
 */
class laveral extends  _base
{


	public function get($filepath){

		if(!$this->check($filepath))return [];
		$excel = Excel::load($filepath);
		$data = $excel ->toArray();
		return $data;
	}


}