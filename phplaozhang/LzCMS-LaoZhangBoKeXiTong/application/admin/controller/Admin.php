<?php
namespace app\admin\controller;

/**
* 
*/
class Admin extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
	}

	function edit(){
		if(request()->isPost()){
			$params = input('post.');
			if($params['password'] && !empty($params['password'])){
				$params['password'] = strtolower(md5($params['password']));
			}else{
				unset($params['password']);
			}
			$result = $this->model->edit($params);
			if($result){
				session(null);
				return json(array('code'=>200,'msg'=>'修改成功'));
			}else{
				return json(array('code'=>0,'msg'=>'修改失败'));
			}
		}
		return view('edit');
	}

}