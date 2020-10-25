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
use Admin\Model\GoodsModel;
class GoodsController extends CommonController{
	
	protected function _initialize(){
		parent::_initialize();
		$this->breadcrumb1='商品';
		$this->breadcrumb2='商品管理';
	}
	
	public function index(){
		$model=new GoodsModel();   
		
		$filter=I('get.');
		
		$search=array();
		
		if(isset($filter['name'])){
			$search['name']=$filter['name'];		
		}
		if(isset($filter['category'])){
			$search['category']=$filter['category'];
			$this->get_category=$search['category'];		
		}
		if(isset($filter['status'])){
			$search['status']=$filter['status'];	
			$this->get_status=$search['status'];			
		}
		
		$data=$model->show_goods_page($search);	
		
		$this->category=M('goods_category')->select();		
		
		$this->assign('empty',$data['empty']);// 赋值数据集
		$this->assign('list',$data['list']);// 赋值数据集
		$this->assign('page',$data['page']);// 赋值分页输出	
		
		$this->display();
	}
	function add(){
		
		if(IS_POST){
		
			$model=new GoodsModel();  
			$data=I('post.');
			//dump($data);die;
			$return=$model->add_goods($data);			
			$this->osc_alert($return);
		}
		
		//库存状态
		$this->stock_status=M('StockStatus')->select();
		//长度单位
		$this->length_class=M('LengthClass')->select();
		//重量单位	
		$this->weight_class=M('WeightClass')->select();
		
		$this->action=U('Goods/add');
		$this->crumbs='新增';
		$this->display('edit');
	}
	
	function edit(){
		
		$model=new GoodsModel();  
		
		if(IS_POST){
			
			$data=I('post.');
			//dump($data);die;
			$return=$model->edit_goods($data);		
		
			$this->osc_alert($return);
		}
		$this->crumbs='编辑';		
		$this->action=U('Goods/edit');
		$this->description=M('goods_description')->find(I('id'));		
		//库存状态
		$this->stock_status=M('StockStatus')->select();
		//长度单位
		$this->length_class=M('LengthClass')->select();
		//重量单位	
		$this->weight_class=M('WeightClass')->select();
		
		$this->goods=$model->get_goods_data(I('id'));
		
		$this->goods_images=$model->get_goods_image_data(I('id'));
		
		$this->goods_discount=M('goods_discount')->where(array('goods_id'=>I('id')))->order('quantity ASC')->select();
		
		$this->goods_categories=$model->get_goods_category_data(I('id'));
		
		$this->goods_options=$model->get_goods_options(I('id'));
		//dump($this->goods_options);die;
		$option_model=new \Admin\Model\OptionModel();
		//选项值
		foreach ($this->goods_options as $goods_option) {
				$option_values[$goods_option['option_id']] = $option_model->getOptionValues($goods_option['option_id']);
		}		
		//dump($this->goods_options);
		//dump($option_values);die;
		$this->option_values=$option_values;
		
		$this->display('edit');		
	}
	
	function copy_goods(){
		$id =I('id');
		$model=new GoodsModel();  
		if($id){		
			foreach ($id as $k => $v) {						
				$model->copy_goods($v);
			}	
			$data['redirect']=U('Goods/index');		
			$this->ajaxReturn($data);
			die;
		}
	}

	function del(){
		$model=new GoodsModel();  
		$return=$model->del_goods(I('get.id'));			
		$this->osc_alert($return); 	
	}	
}
?>