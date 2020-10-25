<?php
namespace app\admin\controller;

/**
* 单页控制器
*/
class Page extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/page');
	}

	function edit(){
		$category_id = input('param.category_id');
		$page = $this->model->where('category_id',$category_id)->find();
		if(request()->isPost()){
			$params = input('post.');
			if(empty($params['description'])){
				$params['description'] = mb_substr(strip_tags($params['content']), 0,250,'utf-8');
			}
			$result = $this->model->edit($params,$page?true:false);
			if($result){
				return json(array('code'=>200,'msg'=>'编辑成功'));
			}else{
				return json(array('code'=>0,'msg'=>'编辑失败'));
			}
		}
		return view('edit',['page'=>$page,'category_id'=>$category_id,'categorys'=>cache('categorys')]);
	}
}