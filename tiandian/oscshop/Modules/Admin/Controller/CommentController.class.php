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
use Admin\Model\CommentModel;
class CommentController extends CommonController{
	
	protected function _initialize(){
		parent::_initialize();
		$this->breadcrumb1='系统';
		$this->breadcrumb2='访客留言';
	}
	
	public function index(){
		$model=new CommentModel();   
		
		$data=$model->show_comment_page();		
		
		$this->assign('empty',$data['empty']);// 赋值数据集
		$this->assign('list',$data['list']);// 赋值数据集
		$this->assign('page',$data['page']);// 赋值分页输出	
		/**/
		$this->display();
	}

}
?>