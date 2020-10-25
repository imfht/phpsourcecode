<?php
namespace app\index\controller;

/**
* 文章控制器类
*/
class Article extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/article');
		$this->category_model = model('common/category');
	}

	function index(){
		$category_id = input('param.category_id');
		$category_ids = $this->category_model->get_category_ids($category_id);//获取所有子分类id
		$articles = $this->model->get_list(['category_id'=>['IN',$category_ids]],'is_top desc,id desc',15,1);
		$recommend_list = $this->model->get_list(['category_id'=>['IN',$category_ids],'is_recommend'=>'1'],'id desc',10); //推荐文章
		$new_list = $this->model->get_list(['category_id'=>['IN',$category_ids]],['id desc'],10); //最新文章 
		$hot_list = $this->model->get_list(['category_id'=>['IN',$category_ids]],['hits desc'],10); //热门文章
		$breadcrumb = $this->category_model->breadcrumb($category_id);
		$second_categorys = $this->category_model->get_second_categorys($category_id);
		$this->seo['title'] = $this->categorys[$category_id]['name'].'-'.$this->seo['title'];
		if($this->categorys[$category_id]['meta_keywords']){ $this->seo['keywords'] = $this->categorys[$category_id]['meta_keywords'].','.$this->seo['keywords'];}
		if($this->categorys[$category_id]['meta_description']){ $this->seo['description'] = $this->categorys[$category_id]['meta_description'];}
		$template = $this->category_model->get_template($category_id,1);
		return view($template,['lists'=>$articles,'page'=>$articles->render(),'recommend_list'=>$recommend_list,'new_list'=>$new_list,'hot_list'=>$hot_list,'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo]);
	}

	function lists(){
		$category_id = input('param.category_id');
		$articles = $this->model->get_list(['category_id'=>$category_id],'is_top desc,id desc',15,1);
		$recommend_list = $this->model->get_list(['category_id'=>$category_id,'is_recommend'=>'1'],'id desc',10); //推荐文章
		$hot_list = $this->model->get_list(['category_id'=>$category_id],['hits desc'],10); //热门文章
		$breadcrumb = $this->category_model->breadcrumb($category_id);
		$second_categorys = $this->category_model->get_second_categorys($category_id);
		$this->seo['title'] = $this->categorys[$category_id]['name'].'-'.$this->seo['title'];
		if($this->categorys[$category_id]['meta_keywords']){ $this->seo['keywords'] = $this->categorys[$category_id]['meta_keywords'].','.$this->seo['keywords'];}
		if($this->categorys[$category_id]['meta_description']){ $this->seo['description'] = $this->categorys[$category_id]['meta_description'];}
		$template = $this->category_model->get_template($category_id,2);
		return view($template,['lists'=>$articles,'page'=>$articles->render(),'recommend_list'=>$recommend_list,'hot_list'=>$hot_list,'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo]);
	}

	function show(){
		$id = input('param.id');
		$article = $this->model->get_details($id);
		$this->model->where('id', $id)->setInc('hits'); //点击量自增一
		$article['hits'] = $article['hits']+1; //点击量加一
		$hot_list = $this->model->get_list(['category_id'=>$article['category_id']],['hits desc'],10); //热门文章
		$breadcrumb = $this->category_model->breadcrumb($article['category_id']).'<a><cite>'.$article['title'].'</cite></a>';
		$second_categorys = $this->category_model->get_second_categorys($article['category_id']);
		$this->seo['title'] = $article['title'].'-'.$this->seo['title'];
		if($article['keywords']){ $this->seo['keywords'] = $article['keywords'].','.$this->seo['keywords'];}
		if($article['description']){ $this->seo['description'] = $article['description'];}
		$template = $this->category_model->get_template($category_id,3);
		return view($template,['data'=>$article,'hot_list'=>$hot_list,'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo]);
	}


}
