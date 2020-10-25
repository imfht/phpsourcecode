<?php

namespace DataComposer\Providers\CONFIG;



/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-4
 * Time: 13:22
 */
class laveral
{

	public function __construct(){
		
	}
	public function getConfig($name){
		return config('DataComposer.' . $name);
	}



}