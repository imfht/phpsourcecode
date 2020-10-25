<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

class Odb{
	private static $odb = array();
	
	/**
	 * @return DB master object
	 */
	public static function db(){
		if( !is_object( self::$odb['db']) ){
			include_once RC_PATH_LIB . 'class.RcDb.php';
			self::$odb['db'] = new RcDb( C('DBMASTER'));
		}
		return self::$odb['db'];
	}
	
	/**
	 * @return DB slave object
	 */
	public static function dbslave(){
		if( !is_object( self::$odb['dbslave']) ){
			include_once RC_PATH_LIB . 'class.RcDb.php';
			self::$odb['dbslave'] = new RcDb( C('DBSLAVE'));
		}
		return self::$odb['dbslave'];
	}
	public static function close(){
		foreach ( (array)self::$odb as $db){
			$db->close();
		}
	}
}
?>
