<?php

namespace DataComposer\Providers\redis;



/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-4
 * Time: 13:22
 */
class laveral extends _base
{

	
	public function __construct($connectstring='default'){
		$con=config('database.redis.'.$connectstring);
		$this->init($con['host'],$con['port'],$con['database']);

	}
	



}