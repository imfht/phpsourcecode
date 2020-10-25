<?php

namespace expand;
class ApiReturn {

	static public $Code = [
		'1'		=> ['1', '操作成功'],
		'0'		=> ['0', '操作失败'],
		'-1'	=> ['-1', 'hash参数无效'],
		'-2'	=> ['-2', '应用AppID非法'],
		'-3'	=> ['-3', '缺少AppToken令牌'],
		'-4'	=> ['-4', 'AppToken令牌无效'],
		'-5'	=> ['-5', 'AppToken令牌过期'],
		'-6'	=> ['-6', '缺少UserToken令牌'],
		'-7'	=> ['-7', 'UserToken令牌无效'],
		'-8'	=> ['-8', 'UserToken令牌过期'],
		'-230'	=> ['-230', '应用已禁用'],
		'-340'	=> ['-340', '请求AppToken令牌次数超额'],
		'-800'	=> ['-800', '没有数据'],
		'-900'	=> ['-900', '参数错误'],
		'-999'	=> ['-999', '系统错误'],
	];

	static public function r($code = 1, $data = null,$info = ''){
		if( array_key_exists($code,ApiReturn::$Code) ){
			if( empty($info) ){
				$info = ApiReturn::$Code[$code][1];
			}else{
				$info = $info;
			}
			$info_arr = [
				'code'=> ApiReturn::$Code[$code][0],
				'info'=>$info,
				'data'=>$data
			];
		}else{
			if( empty($info) ){
				$info = ApiReturn::$Code[1][1];
			}else{
				$info = $info;
			}
			$info_arr = [
				'code'=> $code,
				'info'=> $info,
				'data'=> $data
			];
		}
		return json( $info_arr );
	}

}