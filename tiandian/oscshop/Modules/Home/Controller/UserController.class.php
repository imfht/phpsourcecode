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
namespace Home\Controller;
use Home\Model\OrderModel;
class UserController extends CommonController {
	
	protected function _initialize(){
		parent::_initialize();
        // 获取当前用户ID
        define('UID',is_login());
        if(!UID){// 还没登录 跳转到登录页面
            $this->redirect('/login');
        }

     }
	
	
	function pay_success(){
		$this->display();
	}
	
	function validate_address($d){
		$json=array();
		if (empty($d['name'])) {
			$json['error']['name'] = '收货人必填！！';
		}
		if (empty($d['telephone'])) {		
			$json['error']['telephone'] = '联系电话必填！！';
		}		
		if (empty($d['address'])) {			
			$json['error']['address'] = '地址必填！！';
		}	
		if($d['province_id']==-1){
			$json['error']['area'] = '请选择省份！！';
		}
		if($d['city_id']==-1){
			$json['error']['area'] = '请选择城市！！';
		}
		if($json){
			$this->ajaxReturn($json);
			die;			
		}
	}
	
	function delete_address(){
		
		$r=M('address')->where(array('address_id'=>get_url_id('id')))->delete();
		
		storage_user_action(session('user_auth.uid'),session('user_auth.username'),C('FRONTEND_USER'),'删除了收货地址');
		
		if($r){
			$this->redirect('/address');
		}
	}
	
	//新增地址
	function add_address(){
		$this->action_title='新增地址';		
		$this->province=M('area')->where(array('area_parent_id'=>0))->select();
		
		if(IS_POST){
			$data=I('post.');
			$this->validate_address($data);
			$data['member_id']=session('user_auth.uid');
			$r=M('address')->add($data);
			
			storage_user_action(session('user_auth.uid'),session('user_auth.username'),C('FRONTEND_USER'),'新增了收货地址');
			
			if($r){
				$json=array();
				$json['redirect'] = U('/address');
				$this->ajaxReturn($json);
				die;
			}
		}
		$this->action=U('/add_address');
		$this->display('edit_address');
	}
	
	
	//编辑地址
	function edit_address(){
		$this->action_title='编辑地址';
			
		if(IS_POST){
			$data=I('post.');
			$this->validate_address($data);			
			$data['address_id']=get_url_id('address_id');
			$data['member_id']=session('user_auth.uid');
			$r=M('address')->save($data);
			storage_user_action(session('user_auth.uid'),session('user_auth.username'),C('FRONTEND_USER'),'修改了收货地址');
			if($r){
				$json=array();
				$json['redirect'] = U('/address');
				$this->ajaxReturn($json);
				die;
			}
		}

		$address=M('address')->find(get_url_id('id'));
		
		$this->address=$address;
		
		$this->province=M('area')->where(array('area_parent_id'=>0))->select();
		
		$this->city=M('area')->where(array('area_parent_id'=>$address['province_id']))->select();
		
		$this->country=M('area')->where(array('area_parent_id'=>$address['city_id']))->select();
		
		$this->action=U('/edit_address');		
		
		$this->display();
	}
	
	
	//地址簿
	function address(){
		$model=new OrderModel(); 
		
		$list=$model->get_all_address(session('user_auth.uid'));

		$this->address=$list;
		
		$this->display();		
	}	
	
	//联系方式
	function account(){			
		if(IS_POST){
			$json=array();
			
			$d=I('post.');
			
			if (empty($d['email'])) {
				$json['error']['email'] = '邮箱必填！！';
			}
			
			if(!(strlen($d['email']) > 6 && preg_match("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/", $d['email']))){
				$json['error']['email'] = '请输入正确的邮箱！！';
			}
			
			if (!empty($d['tel'])) {
				if(!preg_match("/^1[3-5,8]{1}[0-9]{9}$/",$d['tel'])){
					$json['error']['tel'] = '请输入正确的手机号码！！';
				}				
			}		
			
			if($json){
				$this->ajaxReturn($json);
				die;			
			}
			
			$account['email']=$d['email'];
			$account['telephone']=$d['tel'];
			
			$r=M('member')->where(array('member_id'=>session('user_auth.uid')))->save($account);
			storage_user_action(session('user_auth.uid'),session('user_auth.username'),C('FRONTEND_USER'),'修改了联系方式');
			if($r){				
				$json['redirect'] = U('/account');
				$this->ajaxReturn($json);
				die;
			}
			
		}
		
		$this->action=U('/account');
		$this->account=M('member')->where(array('member_id'=>session('user_auth.uid')))->find();
		$this->display();
	}	
	
	
	
	//修改密码
	function password(){
		
		if(IS_POST){
			$json=array();
			
			$d=I('post.');
			
			if (empty($d['password'])) {
				$json['error']['password'] = '密码必填！！';
			}
			
			if (empty($d['password_re'])) {		
				$json['error']['password_re'] = '确认密码必填！！';
			}		
			
			if($d['password']!=$d['password_re']){
				$json['error']['password_re'] = '两次密码输入不一致！！';
			}
			
			if($json){
				$this->ajaxReturn($json);
				die;			
			}
			$pwd['pwd']=think_ucenter_encrypt($d['password'],C('PWD_KEY'));
			
			storage_user_action(session('user_auth.uid'),session('user_auth.username'),C('FRONTEND_USER'),'修改了密码');
			
			$r=M('member')->where(array('member_id'=>session('user_auth.uid')))->save($pwd);
			
			if($r){				
				$json['redirect'] = U('/password');
				$this->ajaxReturn($json);
				die;
			}
			
		}
		
		$this->action=U('/password');
		
		$this->display();
	}
	//订单列表
	function order(){
		
		$model=new OrderModel();   	
		
		$data=$model->show_order_page(session('user_auth.uid'));		
				
		$this->assign('empty',$data['empty']);// 赋值数据集
		$this->assign('list',$data['list']);// 赋值数据集
		$this->assign('empty','<tr><td>没有订单</td></tr>');// 赋值数据集
		$this->assign('page',$data['page']);// 赋值分页输出
				
		$this->display();
	}
	//订单详情
	function info(){
		$model=new OrderModel();  
		
		$data=$model->order_info(get_url_id('id'));	
		//dump($data);die;
		storage_user_action(session('user_auth.uid'),session('user_auth.username'),C('FRONTEND_USER'),'查看了订单 '.$data['order'][0]['order_num_alias'].' 详情');
		
		$this->order=$data;
		
		$this->display();
	}
	
	function cancel_order(){
		$model=new OrderModel();  
		
		$model->cancel_order(get_url_id('id'));	
		
		storage_user_action(session('user_auth.uid'),session('user_auth.username'),C('FRONTEND_USER'),'取消了订单  '.$order_id);
	
		$this->redirect('/order');
	}
	
	
}