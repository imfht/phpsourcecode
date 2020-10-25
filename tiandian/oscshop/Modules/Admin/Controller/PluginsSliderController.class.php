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
use Admin\Model\PluginsSliderModel;
class PluginsSliderController extends CommonController {
   	protected function _initialize(){
		parent::_initialize();
			$this->breadcrumb1='插件';
			$this->breadcrumb2='轮播图';
	}
    public function index(){
		$model=new PluginsSliderModel();   
		
		$data=$model->show_slider_page();		
		
		$this->assign('empty',$data['empty']);// 赋值数据集
		$this->assign('list',$data['list']);// 赋值数据集
		$this->assign('page',$data['page']);// 赋值分页输出	
		
    	$this->display();
	}
	
	function add(){
		
		if(IS_POST){
			
			$model=new PluginsSliderModel();  
			$data=I('post.');
			$return=$model->add_slider($data);			
			$this->osc_alert($return);
		}
		
		$this->crumbs='新增';		
		$this->action=U('PluginsSlider/add');
		$this->display('edit');
	}

	function edit(){
		if(IS_POST){
			$model=new PluginsSliderModel();  
			$data=I('post.');
			$return=$model->edit_slider($data);		
		
			$this->osc_alert($return);
		}
		$this->crumbs='编辑';		
		$this->action=U('PluginsSlider/edit');
		$this->slider=M('PluginsSlider')->find(I('id'));
		$this->thumb_image=resize($this->slider['image'], 100, 100);
		$this->display('edit');		
	}
	public function del(){
		$r=M('PluginsSlider')->delete(I('id'));
		if($r){
			$this->redirect('PluginsSlider/index');
		}
	}
}