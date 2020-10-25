<?php
 /**
 +------------------------------------------------------------------------------
 * Framk PHP框架
 +------------------------------------------------------------------------------
 * @package  Framk
 * @author   shawn fon <shawn.fon@gmail.com>
 +------------------------------------------------------------------------------
 */
class Framk {	
	public function __construct() {
 /*
 定义文件夹全局变量
 */		
		//分隔符
 		define ( 'S', DIRECTORY_SEPARATOR );
		//定义框架根目录
		define ( 'FRAMK', dirname(__FILE__).S);
		//定义项目根目录
		if( strlen(APP_NAME)===0 ){
			define('APP_ROOT', ROOT.S);
		}else{
			define('APP_ROOT', ROOT.S.str_replace('/',S,APP_NAME).S ) ;
		}
		//define ( 'APP_ROOT', ( strlen(APP_NAME)===0 ? ROOT.S : ROOT.S.str_replace('/',S,APP_NAME).S ) );		
		
		//循环定义框架各功能目录
		$appFolderName = array ('Action', 'Cache', 'Extend','View','Config');
		foreach ( $appFolderName as $value ) {
			define ( strtoupper ( $value ), APP_ROOT.$value.S );
		}

 /* 
 加载框架必须文件
 */				
		require (FRAMK . '_Function.php');	
		$requireFile = array ('Dispatcher', 'Action','Cache');//加载框架核心文件
		foreach ( $requireFile as $value ) {
			_import($value.'.class.php',1);
		}
		new Dispatcher ();	
	}				
	
 /*  +------------------------------------------------------------------------------ */
}//
?>