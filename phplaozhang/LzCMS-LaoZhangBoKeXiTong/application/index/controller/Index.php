<?php
namespace app\index\controller;

/**
* 首页类
*/
class Index extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->article_model = model('common/article');
	}

	function index(){
		$recommend_list = $this->article_model->get_list(['is_recommend'=>'1'],'is_top desc,id desc',10); //推荐文章
		$new_list = $this->article_model->get_list([],['id desc'],10); //最新文章
		$hot_list = $this->article_model->get_list([],['hits desc'],10); //热门文章
		$links = json_decode($this->settings['links'],true); //友情链接
		return view('index',['links'=>$links,'recommend_list'=>$recommend_list,'new_list'=>$new_list,'hot_list'=>$hot_list]);
	}
}
