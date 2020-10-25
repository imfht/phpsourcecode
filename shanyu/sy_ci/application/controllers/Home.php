<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	protected $home_cache_page=5;
	protected $article_list_limit=10;

	public function index($page=1)
	{
		$this->load->driver('cache');

		//缓存页数
		$article_count=$this->cache->file->get('home_article_count');
		if(! $article_count){
			$this->load->model('article_model');
			$article_count=$this->article_model->get_article_count();
			$this->cache->file->save('home_article_count',$article_count,3600);
		}
		
		$article_list=array();
		$article_page='';
		if($article_count){
			//检测页码
			if($page > ceil($article_count/$this->article_list_limit)) show_404();

			//分页
			$article_page=$this->page_html('/','index',$article_count);

			//缓存前3页文章列表
			$beyong= $page > $this->home_cache_page ? TRUE : FALSE;
			if($beyong){
				$this->load->model('article_model');
				$article_list=$this->article_model->get_article_list($page,$this->article_list_limit);
			}else{
				$article_list=$this->cache->file->get('home_article_list_'.$page);
				if(! $article_list){
					$this->load->model('article_model');
					$article_list=$this->article_model->get_article_list($page,$this->article_list_limit);
					$this->cache->file->save('home_article_list_'.$page,$article_list,3600);
				}
			}
		}


		$this->layout('home',compact('article_list','article_page'));
	}

}
