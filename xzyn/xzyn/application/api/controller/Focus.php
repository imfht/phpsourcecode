<?php

// 关注用户 接口
namespace app\api\controller;

use expand\ApiReturn;

class Focus extends Base {
    public function initialize(){
        parent::initialize();

    }
	//获取关注用户的列表
    public function index($hash) {
		$data = cache('input_'.$hash);	//请求字段
		$usertoken = request()->header('usertoken');
		$uid = \app\common\model\TokenUser::where(['token'=>$usertoken])->value('uid');
		$User = new \app\common\model\User;
		$Focus = new \app\common\model\Focus;
		$UserInfo = new \app\common\model\UserInfo;
		if( $data['type'] == 1 ){	// 关注我的用户数据
			$fuid_arr = $Focus->where(['uid'=>$uid])->column('fuid');
			$userdata = $User->where([ ['id','in',$fuid_arr] ])->order('id DESC')->page($data['page'],$data['number'])->select();
		}else{	// 我关注的用户数据
			$uid_arr = $Focus->where(['fuid'=>$uid])->column('uid');
			$userdata = $User->where([ ['id','in',$uid_arr] ])->order('id DESC')->page($data['page'],$data['number'])->select();
		}
		if( empty($userdata) ){
			return ApiReturn::r('-800');	// 没有数据
		}
		$new_data = [];
		foreach ($userdata as $k => $v) {
			$new_data[$k]['id'] = $v['id'];
			$new_data[$k]['username'] = $v['name']?$v['name']:$v['username'];
			$new_data[$k]['avatar'] = $v['userinfo']['avatar_turn'];
			$new_data[$k]['info'] = $v['userinfo']['info']?$v['userinfo']['info']:'这家伙很懒，什么也没写。';
		}
		return ApiReturn::r(1,$new_data);
	}

	//关注用户
    public function add($hash) {
		$data = cache('input_'.$hash);	//请求字段
		$usertoken = request()->header('usertoken');
		$fuid = \app\common\model\TokenUser::where(['token'=>$usertoken])->value('uid');
		if( $data['uid'] == $fuid ){
			return ApiReturn::r(0,'','不能关注自己！');
		}
		$focus = new \app\common\model\Focus;
		$save = $focus->where(['uid'=>$data['uid'],'fuid'=>$fuid])->find();
		if( $data['type'] == 1 ){
			if( $save ){
				return ApiReturn::r(0,'','你已经关注过了！');
			}else{
				$data['fuid'] = $fuid;
				$add = $focus->allowField(true)->save($data);	//添加数据
				if( $add ){
					return ApiReturn::r(1,'','感谢您的关注！');
				}
			}
		}
		if( $data['type'] == 0 && !empty($save) ){
			$del = $focus->where(['uid'=>$data['uid'],'fuid'=>$fuid])->delete();
			if( $del ){
				return ApiReturn::r(1,'','已经取消关注！');
			}
		}
		return ApiReturn::r(0);
	}

}
