<?php
namespace app\api\controller;

use app\common\model\User;
use app\common\model\UserInfo as UserInfos;
use app\common\model\TokenUser;
use expand\ApiReturn;

class Userinfo extends Base {


    public function initialize(){
        parent::initialize();

    }
	// 用户详细信息接口信息
    public function index($hash) {
    	$apiInfo = cache('apiInfo_'.$hash);	//接口信息
		$data = cache('input_'.$hash);	//请求字段
		$reutrn_data = $this->get_userinfo($data);
		return ApiReturn::r(1, $reutrn_data);
    }
	// 更新用户资料接口
    public function edit($hash) {
    	$apiInfo = cache('apiInfo_'.$hash);	//接口信息
		$data = cache('input_'.$hash);	//请求字段
		foreach ($data as $k => $v) {
			if( $v == '' ){
				unset($data[$k]);
			}
		}
		$usertoken = request()->header('usertoken');
		$uid = TokenUser::where(['token'=>$usertoken])->value('uid');
		$User = new User;
		if( !empty($data['is_share']) ){
			$data['is_share'] = json_encode($data['is_share']);
		}
		$save = $User->allowField(true)->save($data,['id'=>$uid]);
    	if( $save ){
    		$User->userInfo()->allowField(true)->save($data,['uid'=>$uid]);
			$reutrn_data = $this->get_userinfo();
			return ApiReturn::r(1,$reutrn_data,'保存成功');
    	}else{
    		return ApiReturn::r(0);
    	}
	}

    public function get_userinfo($data = '') {
		$usertoken = request()->header('usertoken');
		if( empty($data['uid']) ){
			$uid = TokenUser::where(['token'=>$usertoken])->value('uid');
		}else{
			$uid = $data['uid'];
		}
		$userdata = User::get(['id'=>$uid]);
		$userdata['focus_num'] = $userdata['focus_num'];
		$userdata['focusb_num'] = $userdata['focusb_num'];
		$user_data = $userdata->visible($this->fname)->toArray();	// 根据返回字段来返回
		$user_data['last_time'] = $userdata['last_time_turn'];
		$userinfodata = UserInfos::get(['uid'=>$uid]);
		$userinfodata['avatar'] = $userinfodata['avatar_turn'];
		$userinfodata['wx_imgurl'] = $userinfodata['wx_imgurl_turn'];
	 	unset($userinfodata['id']);
	 	unset($userinfodata['create_time']);
		$userinfo_data = $userinfodata->visible($this->fname)->toArray();	// 根据返回字段来返回
		$reutrn_data = array_merge($user_data,$userinfo_data);
		return $reutrn_data;
	}



}
