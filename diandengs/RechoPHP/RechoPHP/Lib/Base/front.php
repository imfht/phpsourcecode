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
 * author:Recho
 * create date:2011-11-11 10:30
 * last modified date:2011-11-11
 * last modified time:21:30
 */
class FrontController{
	protected $_controller,$_action,$_params,$_body,$_controllerFile;
	static $_instance;
	
	/**
	 * return myself object
	 */
	public static function getInstance(){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self();
        }
		return self::$_instance;
	}
	
	/**
	 * route set
	 */
	private function __construct(){
		$this->_controller = $_GET['mod'] ? ($ucfirst=ucfirst( $_GET['mod'])) :'Index';
		$this->_action = $_GET['act'] ? $_GET['act']:'index';
		if( !empty($_GET['mod']) && $_GET['mod']==$ucfirst) rc::debug()->error('404');
	}
	
	/**
	 * routing
	 */
	public function route(){
		if( file_exists( ($file=WWWROOT."Lib/Action/class.{$this->_controller}Action".EXT))){
			require_once RECHO_PHP."Lib/Action/class.RcAction.php";
			require_once $file;
			if( class_exists( $this->_controller.'Action')){
				$recho = new ReflectionClass( $this->_controller.'Action');
				if( $recho->hasMethod( $this->_action)){
					$rc = $recho->getMethod( $this->_action);
					$fuc = $recho->newInstance();
					$rc->invoke( $fuc);
					return true;
				}
			}
		}elseif( file_exists( ($file=RECHO_PHP."Lib/Action/class.{$this->_controller}Action".EXT))){
			require_once RECHO_PHP."Lib/Action/class.RcAction.php";
			require_once $file;
			if( class_exists( $this->_controller.'Action')){
				$recho = new ReflectionClass( $this->_controller.'Action');
				if( $recho->hasMethod( $this->_action)){
					$rc = $recho->getMethod( $this->_action);
					$fuc = $recho->newInstance();
					$rc->invoke( $fuc);
					return true;
				}
			}
		}
		rc::debug()->error( '404');;
	}
    
    public function getParams(){
    	return $this->_params;
    }
}
