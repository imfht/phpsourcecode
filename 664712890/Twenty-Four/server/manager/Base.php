<?php

namespace app\manager;

class Base{
	
	private static $_objects = [];
	
	public static function instance() {
		$name = static::name();
		
		if( !isset(self::$_objects[$name]) ) {
			self::$_objects[$name] = new $name;
			self::$_objects[$name]->init();
		}
		
		return self::$_objects[$name];
	}
	
	public function init() {
		
		
	}
}