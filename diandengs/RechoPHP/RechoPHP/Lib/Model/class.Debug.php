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

/**
 * debug class
 * $Author: Recho $license: http://www.recho.net/ $
 * $create time: 2012-07-24 09:27
 * $last update time: 2011-11-18 18:50 Recho $
 */
defined('IS_IN') or die('Include Error!');
class Debug{
	
	/**
	 * return error
	 * @param unknown_type $stat
	 */
    public function error($stat, $url='/'){
        switch($stat){
    	  	case 404 :
    	  		require(RC_PATH_LIB.'templates/404.dwt');
    	  		@header('HTTP/1.1 404 Not Found');
    	  		@header('Status: 404 Not Found');
    	  		exit;break;
    	  	case 301 :
    	  		@header('HTTP/1.1 301 Moved Permanently');
    	  		@header('Location:'.$url);
    	  		exit;break;
    	  	default:
    	  		break;
    	}
    }
}