<?php
namespace app\index\controller;

/**
* 搜索控制器类
*/
class Search extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/search');
	}

	function search(){
		$keywords = input('param.keywords');
		$model_id = input('param.model_id')?input('param.model_id'):2;
		$keywords = strip_tags($keywords);
		$model_id = intval($model_id);
		$this->model = model('common/'.$this->models[$model_id]['tablename']);
		$map['title'] = array('like','%'.$keywords.'%'); 
		$lists = $this->model->get_list($map,'id desc',10,1);
		$recommend_list = model('common/article')->get_list(['is_recommend'=>'1'],'id desc',6); //推荐
		$hot_list = $this->model->get_list('',['id desc'],10); //热门
		$breadcrumb = '<a href="/">首页</a><a><cite>搜索</cite></a>';
		return view('search',['lists'=>$lists,'page'=>$lists->render(),'hot_list'=>$hot_list,'recommend_list'=>$recommend_list,'breadcrumb'=>$breadcrumb,'keywords'=>$keywords,'seo'=>$this->seo]);
	}
}
