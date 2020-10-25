<?php defined('SYSPATH') or die('No direct script access.');
//栏目api
class Controller_Article extends Controller_Common {
	public function before(){
		parent::before();
		$this->model['article'] = Model::factory('Article');
	}

	//文章栏目列表
	public function action_getarclist()
	{
		$typeid = $this->request->query('typeid');
		//模型id，第一版暂时不考虑独立模型
		$channelid = $this->request->query('channelid') ? $this->request->query('channelid') : 0;
		$pagesize =  $this->request->query('pagesize') ? $this->request->query('pagesize') : $this->_config->get('pagesize');
		$startid =  $this->request->query('startid') ? $this->request->query('startid') : 0;
		$group =  $this->request->query('group') ? $this->request->query('group') : '';
		//递归获取文章
		$getall =  $this->request->query('getall') ? $this->request->query('getall') : 0;

		$arclist = $this->model['article']->get_arclist($typeid,$pagesize,$startid,$getall,$group);
		if($arclist){
			$this->success($arclist);
		}else{
			$this->error('文章列表获取失败');
		}
	}
	
	
	//取栏目内容
	public function action_getarticle()
	{
		//暂时没考虑独立模型和自定义模型
		$aid = $this->request->query('aid');
		$article = $this->model['article']->get_article($aid);
		if($article){
			$this->success($article);
		}else{
			$this->error('文章获取失败');
		}

	}
	
	
}
