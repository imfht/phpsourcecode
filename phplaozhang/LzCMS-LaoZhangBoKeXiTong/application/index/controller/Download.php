<?php
namespace app\index\controller;

/**
* 下载控制器类
*/
class Download extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/download');
		$this->category_model = model('common/category');
	}

	function index(){
		$category_id = input('param.category_id');
		$category_ids = $this->category_model->get_category_ids($category_id);//获取所有子分类id
		$downloads = $this->model->get_list(['category_id'=>['IN',$category_ids]],'is_top desc,id desc',15,1);
		$breadcrumb = $this->category_model->breadcrumb($category_id);
		$second_categorys = $this->category_model->get_second_categorys($category_id);
		$this->seo['title'] = $this->categorys[$category_id]['name'].'-'.$this->seo['title'];
		if($this->categorys[$category_id]['meta_keywords']){ $this->seo['keywords'] = $this->categorys[$category_id]['meta_keywords'].','.$this->seo['keywords'];}
		if($this->categorys[$category_id]['meta_description']){ $this->seo['description'] = $this->categorys[$category_id]['meta_description'];}
		$template = $this->category_model->get_template($category_id,1);
		return view($template,['lists'=>$downloads,'page'=>$downloads->render(),'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo]);
	}

	function lists(){
		$category_id = input('param.category_id');
		$downloads = $this->model->get_list(['category_id'=>$category_id],'is_top desc,id desc',15,1);
		$breadcrumb = $this->category_model->breadcrumb($category_id);
		$second_categorys = $this->category_model->get_second_categorys($category_id);
		$this->seo['title'] = $this->categorys[$category_id]['name'].'-'.$this->seo['title'];
		if($this->categorys[$category_id]['meta_keywords']){ $this->seo['keywords'] = $this->categorys[$category_id]['meta_keywords'].','.$this->seo['keywords'];}
		if($this->categorys[$category_id]['meta_description']){ $this->seo['description'] = $this->categorys[$category_id]['meta_description'];}
		$template = $this->category_model->get_template($category_id,2);
		return view($template,['lists'=>$downloads,'page'=>$downloads->render(),'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo]);
	}

	function show(){
		$id = input('param.id');
		$download = $this->model->get_details($id);
		$this->model->where('id', $id)->setInc('hits'); //点击量自增一
		$download['hits'] = $download['hits']+1; //点击量加一
		$file_url = $download['file_url'];
		if(parse_url($file_url)['host']){
			var_dump($file_url); 
			$this->redirect($file_url);exit;
		} 
	    if(!preg_match('/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is',$file_url)){
	       $file_url =request()->domain() . $file_url;
	    }
	    if(isset($download['filename'])){
	        $filename = $download['filename'];
	    } else {
	        $filename = basename($file_url);
	    }
	    $mime = 'application/force-download'; 
	    header('Pragma: public');  
	    header('Expires: 0');      
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0'); 
	    header('Cache-Control: private',false); 
	    header('Content-Type: '.$mime); 
	    header('Content-Disposition: attachment; filename="'.$filename.'"'); 
	    header('Content-Transfer-Encoding: binary'); 
	    header('Connection: close'); 
	    readfile($file_url);
	}

}
