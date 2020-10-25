<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 *
 * @author edison.an
 *        
 */
class Controller extends BaseController {
	const OK_CODE = 9999;
	const SYSTEM_ERROR_CODE = 1000;
	
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	public function responseJson($code, $result = '', $msg = '') {
		$resp = array (
				'code' => $code,
				'msg' => $msg,
				'result' => $result 
		);
		return json_encode ( $resp );
	}
}
