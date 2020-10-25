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
 *会员账户资料相关
 */
namespace osc\member\controller;
use osc\common\controller\MemberBase;
use think\Db;
class Account extends MemberBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','个人资料');
		
	}
	//我的资料
    public function profile(){	
		
		if(request()->isPost()){
			
			$data=input('post.');			
			
			$member['userpic']=$data['userpic'];
			$member['nickname']=$data['nickname'];			
			$member['email']=$data['email'];
							
			if(Db::name('member')->where('uid',UID)->update($member,false,true)){
				
				storage_user_action(UID,member('username'),config('FRONTEND_USER'),'修改了系统个人资料');	
				
				return ['success'=>'修改成功','action'=>'edit'];
			}else{
				return ['error'=>'修改失败'];
			}
		}
		
		$this->assign('user',osc_service('member','user')->user_info());
		
		$this->assign('breadcrumb2','我的资料');
		
		return $this->fetch();   
    }
	//修改密码
	public function password(){
		if(request()->isPost()){
			
			$data=input('post.');
			
			if(empty($data['old_pwd'])){
				return ['error'=>'请输入旧密码'];
			}elseif(empty($data['new_pwd'])){
				return ['error'=>'请输入新密码'];
			}elseif(empty($data['new_pwd2'])){
				return ['error'=>'请输入新密码确认'];
			}elseif($data['new_pwd2']!=$data['new_pwd']){
				return ['error'=>'两次密码输入不一致'];
			}
		
			$user_info=osc_service('member','user')->user_info();
			
			if(think_ucenter_encrypt($data['old_pwd'],config('PWD_KEY'))!=$user_info['password']){
				return ['error'=>'旧密码错误'];
			}
			
			$member['password']=think_ucenter_encrypt($data['new_pwd'],config('PWD_KEY'));	
							
			if(Db::name('member')->where('uid',UID)->update($member,false,true)){
				
				storage_user_action(UID,member('username'),config('FRONTEND_USER'),'修改了登录密码');	
				
				return ['success'=>'修改成功','action'=>'edit'];
			}else{
				return ['error'=>'修改失败'];
			}
		}
		$this->assign('breadcrumb2','修改密码');
		return $this->fetch(); 
	}

	function address(){
		
		$this->assign('list',Db::name('address')->where('uid',UID)->paginate(config('page_num')));
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		$this->assign('breadcrumb2','个人资料');
		$this->assign('crumbs','地址簿');
		return $this->fetch(); 
	}
	
	function add_address(){
		
		if(request()->isPost()){
			$data=input('post.');
			$validate=new \osc\member\validate\Shipping();		
			
			if (!$validate->check($data)) {			    
				return ['error'=>$validate->getError()];
			}			
			$data['uid']=UID;
			
			if(osc_service('member','user')->add_address($data)){
				return ['redirect'=>url('Account/address')];
			}else{
				return ['error'=>'新增失败'];
			}
		}
		$this->assign('action',url('Account/add_address'));
		$this->assign('province',Db::name('area')->where(array('area_parent_id'=>0))->select());		
		$this->assign('breadcrumb2','新增');
		$this->assign('breadcrumb1','地址簿');
		return $this->fetch('edit_address'); 
	}
	function edit_address(){
		
		if(request()->isPost()){
			$data=input('post.');
			$validate=new \osc\member\validate\Shipping();		
			
			if (!$validate->check($data)) {			    
				return ['error'=>$validate->getError()];
			}			
			$data['uid']=UID;
			
			storage_user_action(UID,member('username'),config('FRONTEND_USER'),'修改了收货地址');
			if(Db::name('address')->update($data)){
				return ['redirect'=>url('Account/address')];
			}else{
				return ['error'=>'修改失败'];
			}
		}
		
		$map['uid']=['eq',UID];
		$map['address_id']=['eq',(int)input('param.id')];

		if(!$address=Db::name('address')->where($map)->find()){
			$this->error('非法操作！！');
		}
		$this->assign('address',$address);
		
		$this->assign('province',Db::name('area')->where(array('area_parent_id'=>0))->select());
		$this->assign('city',Db::name('area')->where(array('area_parent_id'=>$address['province_id']))->select());
		$this->assign('country',Db::name('area')->where(array('area_parent_id'=>$address['city_id']))->select());
		
		$this->assign('breadcrumb2','编辑');
		$this->assign('breadcrumb1','地址簿');
		$this->assign('action',url('Account/edit_address'));
		
		return $this->fetch(); 
	}
	function del_address(){
		$map['uid']=['eq',UID];
		$map['address_id']=['eq',(int)input('param.id')];
		
		if(Db::name('address')->where($map)->delete()){
			storage_user_action(UID,member('username'),config('FRONTEND_USER'),'删除了收货地址');
			$this->success('删除成功！！',url('Account/address'));
		}else{
			$this->error('删除失败！！');
		}
		
		
	}
}
