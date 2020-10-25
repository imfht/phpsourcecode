<?php

namespace app\libs;
use app\helpers\Logger;

class Router{
	
	public function run($params) {
		
		$path = explode('/', $params['path']);
		
		$controller = '\app\controllers\\'.ucfirst($path[0]);
		$action = 'action'.ucfirst($path[1]);
		
		if( class_exists($controller) && method_exists($controller, $action) ) {
			$obj = new $controller($params);
			
			if($obj->beforeAction()) {
				$obj->{$action}();
				$obj->afterAction();
			}
			
			$obj = null;
		} else {
			Logger::add('No Path:' . $params['path']);
		}
		
	}
	
}