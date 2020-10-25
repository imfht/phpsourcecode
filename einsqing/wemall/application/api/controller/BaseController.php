<?php
namespace app\api\controller;

use think\Controller;

class BaseController extends Controller
{
    public function _initialize(){
        if (request()->isOptions()){
            abort(json(true,200));
        }
    }

	//根据token获取user_id
    public function get_user_id(){
    	$token = request()->header('Authorization');

    	$config = model('app\common\model\Config')->find()->toArray();
    	if($config['debug']){
    		return 1;
    	}

    	if($token){
	        try {
	            $validator = new \Gamegos\JWT\Validator();
	            $token = $validator->validate($token, config('jwt_key'));
	            $token = $token->getClaims();//信息
	            // dump($token->getHeaders());
	            return $token['user_id'];
	        } catch (\Gamegos\JWT\Exception\JWTException $e) {
	            // dump($e->getMessage());//验证失败
	            abort(json(["msg" => "无效的token", "code" => 0], 401));
	        }
    	}else{
    		abort(json(["msg" => "未登陆", "code" => 0], 401));
    	}
    }


    
}