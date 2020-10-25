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
namespace osc\admin\controller;
use osc\common\controller\AdminBase;
use think\Db;
class User extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','系统');
		$this->assign('breadcrumb2','系统用户');
	}
	
    public function index()
    {		
		$list = Db::view('Admin','admin_id,user_name,status')
		->view('AuthGroupAccess','group_id','Admin.admin_id=AuthGroupAccess.uid')
		->view('AuthGroup','title','AuthGroupAccess.group_id=AuthGroup.id')
		->where('user_name','neq',config('administrator'))
		->order('admin_id desc')
		->paginate(config('page_num'));	
		$this->assign('empty', '<tr><td colspan="20">~~暂无数据</td></tr>');
		$this->assign('list',$list);
		    
		return $this->fetch();   
    }
	public function add(){
		
		if(request()->isPost()){
				
			$data=input('post.');	
			$result = $this->validate($data,'User');			
			if($result!==true){
				return ['error'=>$result];
			}
			
			$admin['user_name']=$data['user_name'];
			$admin['true_name']=$data['true_name'];
			$admin['telephone']=$data['telephone'];
			$admin['status']=$data['status'];
			$admin['create_time']=time();
			
			$admin['group_id']=$data['group_id'];
			
			$admin['passwd']=think_ucenter_encrypt($data['passwd'],config('PWD_KEY'));
			
			$admin_id=Db::name('admin')->insert($admin,false,true);
			
			if($admin_id){
				Db::name('auth_group_access')->insert(['uid'=>$admin_id,'group_id'=>$data['group_id']],false,true);
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'新增了系统用户');
				return ['success'=>'新增成功','action'=>'add'];
			}else{
				return ['error'=>'新增失败'];
			}
			
		}
		
		$this->assign('group',Db::name('auth_group')->field('id,title')->select());
		
		$this->assign('crumbs','新增');
		$this->assign('action',url('User/add'));
		
		return $this->fetch('edit'); 
	}
	public function edit(){
		
		if(request()->isPost()){
			$data=input('post.');
						
			$admin['admin_id']=$data['admin_id'];
			$admin['user_name']=$data['user_name'];
			$admin['true_name']=$data['true_name'];
			$admin['telephone']=$data['telephone'];
			$admin['status']=$data['status'];
			
			$admin['group_id']=$data['group_id'];
			
			if(!empty($data['passwd']))
			$admin['passwd']=think_ucenter_encrypt($data['passwd'],config('PWD_KEY'));
			
			$admin['update_time']=time();
						
			if(Db::name('admin')->update($admin,false,true)){
				
				Db::name('auth_group_access')->where('uid',$data['admin_id'])->update(['group_id'=>$data['group_id']],false,true);
				
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'修改了系统用户');
				
				return ['success'=>'修改成功','action'=>'edit'];
			}else{
				return ['error'=>'修改失败'];
			}
			
		}
		
		$this->assign('group',Db::name('auth_group')->field('id,title')->select());
		

		$this->assign('user',Db::name('admin')->where('admin_id',input('id'))->find());
		
		$this->assign('crumbs','修改');
		$this->assign('action',url('User/edit'));
		
		return $this->fetch('edit'); 
	}
	public function del(){
		$data=input('param.');
		
		Db::name('auth_group_access')->where('uid',$data['id'])->delete();		
		Db::name('admin')->where('admin_id',$data['id'])->delete();	
		
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除了系统用户');	
		
		$this->redirect('User/index');
	}
	public function set_status(){
		$data=input('param.');
		
		Db::name('admin')->where('admin_id',$data['id'])->update(['status'=>$data['status']],false,true);		
		
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'修改了系统用户状态');	
		
		$this->redirect('User/index');
	}
}
