<?php
namespace app\api\controller;

use app\common\model\User;
use expand\ApiReturn;

// 获取用户UserToken令牌

class GetUserToken extends Base {

    public function initialize() {
        parent::initialize();

	}
	// 获取用户UserToken令牌（用户登录）
    public function index($hash) {
		$data = cache('input_'.$hash);	//请求字段
		$User = new User();
		$login = $User->login( $data['username'],md5($data['password']) );
		if( $login ){
			$config = new \app\common\model\Config();
			$login_time = $config->where(['type'=>'system', 'k'=>'login_time'])->value('v');
			$returndata['expire'] = $login_time;
			$returndata['userToken'] = session('user_token');
			return ApiReturn::r(1, $returndata);
		}else{
			return ApiReturn::r(0,'',$User->error);
		}
    }
	// 用户注册
    public function register($hash) {
		$data = cache('input_'.$hash);	//请求字段
		$User = new User();
		$result = $this->validate($data,'user.register');
		if( true !== $result ){
			return ApiReturn::r(0,'',$result);
		}else{
			$add = $User->allowField(true)->save($data);
			$uid = $User->id;
		}
		if( $add ){
			$User->userInfo()->save( ['uid'=>$uid] );
			return ApiReturn::r(1,'','注册成功' );
		}else{
			return ApiReturn::r(0,'','注册失败');
		}
    }
}
