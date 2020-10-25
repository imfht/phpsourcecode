<?php
namespace app\index\controller;

/**
* 图集控制器类
*/
class Picture extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/picture');
		$this->category_model = model('common/category');
	}

	function index(){
		$category_id = input('param.category_id');
		$category_ids = $this->category_model->get_category_ids($category_id);//获取所有子分类id
		$pictures = $this->model->get_list(['category_id'=>['IN',$category_ids]],'is_top desc,id desc',16,1);
		$breadcrumb = $this->category_model->breadcrumb($category_id);
		$second_categorys = $this->category_model->get_second_categorys($category_id);
		$this->seo['title'] = $this->categorys[$category_id]['name'].'-'.$this->seo['title'];
		if($this->categorys[$category_id]['meta_keywords']){ $this->seo['keywords'] = $this->categorys[$category_id]['meta_keywords'].','.$this->seo['keywords'];}
		if($this->categorys[$category_id]['meta_description']){ $this->seo['description'] = $this->categorys[$category_id]['meta_description'];}
		$template = $this->category_model->get_template($category_id,1);
		return view($template,['lists'=>$pictures,'page'=>$pictures->render(),'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo]);
	}

	function lists(){
		$category_id = input('param.category_id');
		$pictures = $this->model->get_list(['category_id'=>$category_id],'is_top desc,id desc',16,1);
		$breadcrumb = $this->category_model->breadcrumb($category_id);
		$second_categorys = $this->category_model->get_second_categorys($category_id);
		$this->seo['title'] = $this->categorys[$category_id]['name'].'-'.$this->seo['title'];
		if($this->categorys[$category_id]['meta_keywords']){ $this->seo['keywords'] = $this->categorys[$category_id]['meta_keywords'].','.$this->seo['keywords'];}
		if($this->categorys[$category_id]['meta_description']){ $this->seo['description'] = $this->categorys[$category_id]['meta_description'];}
		$template = $this->category_model->get_template($category_id,2);
		return view($template,['lists'=>$pictures,'page'=>$pictures->render(),'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo]);
	}
 
	function show(){
		$id = input('param.id');
		$picture = $this->model->get_details($id);
		$this->model->where('id', $id)->setInc('hits'); //点击量自增一
		$picture['hits'] = $picture['hits']+1; //点击量加一
		$hot_list = $this->model->get_list(['category_id'=>$picture['category_id']],['hits desc'],10); //热门
		$breadcrumb = $this->category_model->breadcrumb($picture['category_id']).'<a><cite>'.$picture['title'].'</cite></a>';
		$second_categorys = $this->category_model->get_second_categorys($picture['category_id']);
		$this->seo['title'] = $picture['title'].'-'.$this->seo['title'];
		if($picture['keywords']){ $this->seo['keywords'] = $picture['keywords'].','.$this->seo['keywords'];}
		if($picture['description']){ $this->seo['description'] = $picture['description'];}
		$template = $this->category_model->get_template($category_id,3);
		return view($template,['data'=>$picture,'hot_list'=>$hot_list,'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo]);
	}

}
