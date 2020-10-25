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
use Admin\Model\OrderModel;
class OrderController extends CommonController{
	
	protected function _initialize(){
		parent::_initialize();
			$this->breadcrumb1='订单';
			$this->breadcrumb2='订单管理';
	}
	
     public function index(){
     	
		$model=new OrderModel();   
		
		$filter=I('get.');
		
		$search=array();
		
		if(isset($filter['order_num'])){
			$search['order_num']=$filter['order_num'];
		
		}
		if(isset($filter['user_name'])){
			$search['user_name']=$filter['user_name'];
					
		}
		if(isset($filter['status'])){
			$search['status']=$filter['status'];
			$this->get_status=$search['status'];	
		}
		
		$data=$model->show_order_page($search);		
		
		$this->assign('empty',$data['empty']);// 赋值数据集
		$this->assign('list',$data['list']);// 赋值数据集
		$this->assign('page',$data['page']);// 赋值分页输出	
		
		$this->status=M('order_status')->select();
		
    	$this->display();
	 }
	 
	 function print_order(){
	 	$model=new OrderModel();   
		
		$this->order=$model->order_info(I('id'));
		$this->print=true;
		$this->display('./Themes/Home/default/Mail/order.html');
	 }
	 
	 public function show_order(){
	 	
	 	$this->crumbs='订单详情';
		
	 	$model=new OrderModel();   
		
		$this->data=$model->order_info(I('id'));
		
	 	$this->display('show');
	 }
	 function history(){
	 		$model=new OrderModel();
			
			if(IS_POST){				
				
				if(I('order_status_id')==C('cancel_order_status_id')){
					$Order = new \Home\Model\OrderModel();
					$Order->cancel_order($_GET['id']);					
					storage_user_action(session('user_auth.uid'),session('user_auth.username'),C('BACKEND_USER'),'取消了订单  '.$_GET['id']);
					$result=true;
				}else{
					$result=$model->addOrderHistory($_GET['id'],$_POST);
				}
					
			/**	
			 * 判断是否选择了通知会员，并发送邮件
			 */
			if(I('notify')==1){
				$order_info=M('order')->find($_GET['id']);
				
				$status=get_order_status_name(I('order_status_id'));
				
				$model=new \Admin\Model\OrderModel();	   
			    $this->order=$model->order_info($_GET['id']);
				$this->seller_comment=$_POST['comment'];
			    $html=$this->fetch('./Themes/Home/default/Mail/order.html');				   
			    think_send_mail($order_info['email'],$order_info['name'],'订单-'.$status.'-'.C('SITE_NAME'),$html); 
			}
				
				if($result){
					$this->success='新增成功！！';
				}else{
					$this->error='新增失败！！';
				}
			}
			
			$results = $model->getOrderHistories($_GET['id']);
		
			foreach ($results as $result) {
				$histories[] = array(
					'notify'     => $result['notify'] ? '是' : '否',
					'status'     => $result['status'],
					'comment'    => nl2br($result['comment']),
					'date_added' => date('Y/m/d H:i:s', $result['date_added'])
				);
			}	
			
			$this->histories=$histories;
			
			$this->display();
	}
	
	function del(){
		$model=new OrderModel();  
		$return=$model->del_order(I('get.id'));			
		$this->osc_alert($return); 	
	}	
		
}
?>