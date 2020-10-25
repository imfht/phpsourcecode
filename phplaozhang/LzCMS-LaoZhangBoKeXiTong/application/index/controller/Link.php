<?php
namespace app\index\controller;

/**
* 链接控制器类
*/
class Link extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/link');
		$this->category_model = model('common/category');
	}


	function lists(){
		$category_id = input('param.category_id');
		$links = $this->model->get_list(['category_id'=>$category_id],'is_top desc,id desc',15,1);
		$breadcrumb = $this->category_model->breadcrumb($category_id);
		$second_categorys = $this->category_model->get_second_categorys($category_id);
		$this->seo['title'] = $this->categorys[$category_id]['name'].'-'.$this->seo['title'];
		if($this->categorys[$category_id]['meta_keywords']){ $this->seo['keywords'] = $this->categorys[$category_id]['meta_keywords'].','.$this->seo['keywords'];}
		if($this->categorys[$category_id]['meta_description']){ $this->seo['description'] = $this->categorys[$category_id]['meta_description'];}
		$template = $this->category_model->get_template($category_id,2);
		return view($template,['lists'=>$links,'page'=>$links->render(),'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo]);
	}



}
