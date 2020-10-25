<?php
namespace app\admin\controller;

/**
* 站点设置控制器类
*/
class Cache extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model=model('common/cache');
	}

	function update(){
		$result = $this->model->update_cache();
		if($result){
			return json(array('code'=>200,'msg'=>'更新缓存成功'));
		}else{
			return json(array('code'=>0,'msg'=>'更新缓存失败'));
		}
	}
}