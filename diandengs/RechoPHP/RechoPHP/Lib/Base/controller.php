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
 * front controller
 * 
 * author:Recho
 * last modified date:2011-11-12
 * last modified time:00:30
 */

class Controller {
    public $fc, $params;
    
    //初始化数据
    function __construct(){
        $this->fc = FrontController::getInstance();
        $this->params = $this->fc->getParams();
    }
  
    //错误提示
    public function error($stat){
    	  switch($stat){
    	  	case 404 :
				   	 require(APPPATH.'views/404.dwt');
				     @header('HTTP/1.1 404 Not Found');
				     @header('Status: 404 Not Found');
				     exit;
    	  		break;
    	  	case 417 :
				   	 require(APPPATH.'views/404.dwt');
				     @header('HTTP/1.1 404 Not Found');
				     @header('Status: 404 Not Found');
				     exit;
    	  		break;		
    	  	default:
    	  		break;
    	  }
    }
}