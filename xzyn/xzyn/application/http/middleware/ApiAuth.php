<?php
// 应用 处理Api接入认证 前置行为的中间件

namespace app\http\middleware;

use app\common\model\TokenUser;

// use think\Validate;

// 处理Api接入认证
class ApiAuth {

    public function handle($request, \Closure $next){
		$TokenUser = new TokenUser;

		$usertoken = $request->header('usertoken');
		if( empty($usertoken) ){
			return x_return('-6');
		}
		$tokenData = $TokenUser->get(['token'=>$usertoken]);
		if( empty($tokenData) ){
			return x_return('-7');
		}
		if( $tokenData['token_time'] < time() ){   //UserToken令牌过期
			return x_return('-8');
		}
		cache('token_info',$tokenData,3600);  // 缓存用户token信息
		cache('user_data',$tokenData->user,3600);  // 缓存用户信息
		cache('user_info',$tokenData->user_info,3600);  // 缓存用户信息
		$login_time = x_get_config('login_time','system');
		$token_time = time() + $login_time;
		$data = ['token_time' => $token_time];
		$TokenUser->where(['uid'=>$tokenData['uid'], 'type'=>'1'])->update($data);

		return $next($request);
    }

}
