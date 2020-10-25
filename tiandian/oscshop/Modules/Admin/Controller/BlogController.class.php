<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Admin\Controller;
use Admin\Model\BlogModel;
class BlogController extends CommonController{
	
	protected function _initialize(){
		parent::_initialize();
		$this->breadcrumb1='博客';
		$this->breadcrumb2='博客列表';
	}
	
	public function index(){
		$model=new BlogModel();   
		
		$data=$model->show_blog_page();		
		
		$this->assign('empty',$data['empty']);// 赋值数据集
		$this->assign('list',$data['list']);// 赋值数据集
		$this->assign('page',$data['page']);// 赋值分页输出	
		/**/
		$this->display();
	}
	function add(){
		
		if(IS_POST){
			
			$model=new BlogModel();  
			$data=I('post.');
			$return=$model->add_blog($data);			
			$this->osc_alert($return);
		}
		$this->action=U('Blog/add');
		$this->crumbs='新增';
		$this->display('edit');
	}
	
	function edit(){
		
		$model=new BlogModel();  
		
		if(IS_POST){
			
			$data=I('post.');
			$return=$model->edit_blog($data);		
		
			$this->osc_alert($return);
		}
		$this->crumbs='编辑';		
		$this->action=U('Blog/edit');
		$this->blog_content=M('blog_content')->where(array('blog_id'=>I('id')))->find();
		
		$this->blog=$model->get_blog_data(I('id'));
		
		$this->blog_images=$model->get_blog_image_data(I('id'));
		
		$this->blog_categories=$model->get_blog_category_data(I('id'));
		
		$this->display('edit');		
	}
	

	function del(){
		$model=new BlogModel();  
		$return=$model->del_blog(I('get.id'));			
		$this->osc_alert($return); 	
	}	
}
?>