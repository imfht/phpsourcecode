<?php
namespace root\base;
class ctrl{
	public static function _404(){
		$tpl = P_CORE . 'tpl/404.tpl';
		ob_end_clean();
		require $tpl;
		die;
	}
	
	public static function _500(){
		$tpl = P_CORE . 'tpl/500.tpl';
		ob_end_clean();
		require $tpl;
		die;
	}
}