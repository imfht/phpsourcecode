<?php

namespace App\Http\Utils;

/**
 * 错误码
 *
 * @author edison.an
 *        
 */
class ErrorCodeUtil {
	const OK_CODE = 9999;
	const SYSTEM_ERROR_CODE = 1000;
	const THIRD_NOT_EXSIT = 2100;
	public static function getMessage($code) {
		$arr = array (
				self::CODE_THIRD_NOT_EXSIT => '查询第三方服务记录不存在' 
		);
		return isset ( $arr [$code] ) ? $arr [$code] : 'unkonw error:' . $code;
	}
}