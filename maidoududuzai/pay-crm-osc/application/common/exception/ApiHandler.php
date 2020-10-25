<?php

namespace app\common\exception;

use \Exception;
use \think\exception\Handle;

class ApiHandler extends Handle {

	public function render(Exception $e)
	{

		//return response(make_json(0, $e->getMessage()));

		if(!is_callable([$e, 'getStatusCode'])) {
			return response(make_json(0, $e->getMessage()), 500);
		} else {
			return response(make_json(0, $e->getMessage()), $e->getStatusCode());
		}

	}

}

