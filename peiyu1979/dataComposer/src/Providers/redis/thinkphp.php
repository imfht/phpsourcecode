<?php

namespace DataComposer\Providers\redis;



/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-4
 * Time: 13:22
 */
class thinkphp  extends _base
{

	
	public function __construct($connectstring){
		$con=config('database.'.$connectstring);
		$this->init($con['hostname'],$con['hostport'],$con['database']);

	}
	



}