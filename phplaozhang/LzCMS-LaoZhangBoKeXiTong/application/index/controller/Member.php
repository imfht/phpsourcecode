<?php
namespace app\index\controller;

/**
* 会员类类
*/
class Member extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/member');
	}

	function other_login(){

		return view('other_login');
	}

	function do_other_login(){
		$params = input('post.');
		$member = $this->model->get(['openid' => $params['openid']]);
		if($member){
			session('member',$member);
			//更改登陆时间
			$result = $this->model->isUpdate(true)->allowField(true)->save(['id' => $member['id'],'last_login_time' => date('Y-m-d H:i:s')]);

		}else{
			$result = $this->model->add($params);
			$member = $this->model->get(['openid' => $params['openid']]);
		}
		return json(array('code'=>200,'msg'=>'登陆成功','data'=>$member));
	}

	function is_login(){
		if(session('member.id')){
			return json(array('code'=>200,'msg'=>'已登陆'));
		}else{
			return json(array('code'=>0,'msg'=>'未登录'));
		}
	}

	function logout(){
		session('member',null);
		if(!session('?member')){
			return json(array('code'=>200,'msg'=>'退出成功'));
		}else{
			return json(array('code'=>0,'msg'=>'退出失败'));
		}
	}

}
