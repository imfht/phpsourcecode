<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-20
 * Time: 11:16
 */

namespace DataComposer;
use Cache;

class comm
{
	public static function getPkeyCkey($relationKey){

		$pkey=null;
		$ckey=null;
		$eq=false;

		if(is_array($relationKey)){
			foreach ($relationKey as $_pkey => $_ckey) {
				$pkey=$_pkey;
				$ckey=$_ckey;
			}
		}
		elseif(is_string($relationKey)){
			$pkey=$ckey=$relationKey;
			$eq=true;
		}
		else{
			throw new \Exception("relationKey error");
		}
		return [$pkey,$ckey,$eq];
	}

	public static $cachePrefix='d_comp_';
	public static function cacheSet($fwtype,$key,$value,$expire=10){
		switch ($fwtype){
			case "laveral":
				return Cache::put(self::$cachePrefix.$key,$value,$expire);
				break;
			case "thinkphp":
				return Cache::set(self::$cachePrefix.$key,$value,$expire*60);
				break;
		}

	}
	public static function cacheGet($fwtype,$key){
		return Cache::get(self::$cachePrefix.$key);
	}

	public static function get_object_vars($arr){
		$List=[];
		foreach ($arr as $obj) {
			$List[] = get_object_vars($obj);

		}
		return $List;
	}

}