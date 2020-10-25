<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article extends MY_Controller {

	protected $article_list_limit=10;

	//TODO_RSS
	public function index($name='',$page=1)
	{
		//栏目
		$category_id=$this->check_category_name($name);

		//页码检测
		$this->load->model('article_model');
		$article_map='cid='.$category_id;
		$article_count=$this->article_model->get_article_count($article_map);

		$article_list=array();
		$article_page='';
		if($article_count){
			//分页
			if($page > ceil($article_count/$this->article_list_limit)) show_404();
			$article_page=$this->page_html('/article/',$name,$article_count);

			//文章列表
			$article_list=$this->article_model->get_article_list($page,$this->article_list_limit,$article_map);
		}

		//NAV导航
		$category_list=config_item('category_list');
		$nav[]=array('title'=>$category_list[$category_id]['title'],'url'=>'/article/'.$name.'.html');

		$this->layout('home',compact('article_list','article_page','nav'));
	}

	private function check_category_name($name=''){
		if(!$name) show_404();
		$category_names=config_item('category_names');
		if(!isset($category_names[$name])) show_404();
		return $category_names[$name];
	}

	public function detail($name='',$id=0)
	{
		//栏目
		$category_id=$this->check_category_name($name);
		$article_map='cid='.$category_id.' AND id='.$id;

		//文章
		$this->load->model('article_model');
		$article=$this->article_model->get_article_detail($article_map);
		if(!$article) show_404();

		//处理文章内容
		$article['content']=htmlspecialchars_decode($article['content']);
		$article['add_time']=date('Y-m-d H:i:s',$article['add_time']);

		//NAV导航
		$category_list=config_item('category_list');
		$nav[]=array('title'=>$category_list[$category_id]['title'],'url'=>'/article/'.$name.'.html');
		$nav[]=array('title'=>$article['title'],'url'=>'/article/'.$name.'/'.$id.'.html');

		$this->layout('article',compact('article','nav'));

	}

	public function rss($name=''){
		show_404();
	}

}
