<?php

namespace DataComposer\Providers\CONFIG;


use Config;
use Env;
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-4
 * Time: 13:22
 */
class thinkphp
{
	protected $conf=[];

	public function __construct(){
		
		
	}
	public function getConfig($name){
		if(!array_key_exists($name,$this->conf)) {
			$fn=Env::get('config_path').'DataComposer/'.$name.'.php';
			if(is_file($fn)) $this->conf[$name]= require($fn);
			else return [];
		}
		return $this->conf[$name];
	
		
	}



}