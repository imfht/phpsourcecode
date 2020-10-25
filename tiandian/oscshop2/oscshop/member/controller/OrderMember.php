<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */ 
namespace osc\member\controller;
use osc\common\controller\MemberBase;
use think\Db;
class OrderMember extends MemberBase{
	
	protected function _initialize(){
		parent::_initialize();
		
		$this->assign('breadcrumb2','订单管理');
		$this->assign('breadcrumb1','我的订单');
		
	}
	
	function index(){
		$this->assign('status',Db::name('order_status')->select());
		$this->assign('list',osc_order()->order_list(input('param.'),20,member('uid')));
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		return $this->fetch();
	}
	
	public function show_order(){
     	
		if(!$order=osc_order()->order_info(input('param.id'),member('uid'))){
			$this->error('非法操作！！');
		}
		storage_user_action(UID,member('username'),config('FRONTEND_USER'),'查看了订单详情');
		$this->assign('data',$order);		
		$this->assign('crumbs','订单详情');
				
    	return $this->fetch('show');
	 }
	 public function history(){
		
	 		$model=osc_order();		
			
			$results = $model->get_order_histories(input('param.id'),member('uid'));
		
			foreach ($results as $result) {
				$histories[] = array(
					'notify'     => $result['notify'] ? '是' : '否',
					'status'     => $result['name'],
					'comment'    => nl2br($result['comment']),
					'date_added' => date('Y/m/d H:i:s', $result['date_added'])
				);
			}	
			
			$this->histories=$histories;

			$this->assign('histories',$histories);	
			
			exit($this->fetch());
	}
	function cancel(){				
		osc_order()->cancel_order((int)input('param.id'),UID);
		storage_user_action(UID,member('username'),config('FRONTEND_USER'),'取消了订单');
		$this->success('取消成功！！',url('OrderMember/index'));
	}
}
