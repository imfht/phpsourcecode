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
 * 会员权限管理
 */
namespace osc\member\controller;
use osc\common\controller\AdminBase;
use think\Db;
class AuthBackend extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','会员');
		$this->assign('breadcrumb2','权限管理');
	}
	
     public function index(){
     	
     	$list = Db::name('member_auth_group')->paginate(config('page_num'));
		
		$this->assign('list',$list);
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
    	return $this->fetch();
	 }
	
	public function create_group(){

		if(request()->isPost()){			  
			return $this->single_table_insert('MemberAuthGroup','添加了会员用户组');
		}
		
		$this->assign('breadcrumb2','新增用户组');
		$this->assign('action',url('AuthBackend/create_group'));
		return $this->fetch('edit_group');
		
	}
	
	function edit_group(){
		if(request()->isPost()){
			return $this->single_table_update('MemberAuthGroup','修改了会员用户组');
		}	
		
		$this->assign('group',Db::name('member_auth_group')->find(input('id')));
		$this->assign('breadcrumb2','编辑');
		$this->assign('action',url('AuthBackend/edit_group'));
		return $this->fetch('edit_group');		
	}
	
	public function del_group(){
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除了会员用户组');					
		Db::name('member_auth_group')->delete(input('id'));
		$this->redirect('AuthBackend/index');		
	}
	
	public function access(){
		
		$menu=Db::name('member_menu')->order('sort_order')->select();		
		
		
		$this->assign('access_menu',list_to_tree($menu,'id','pid','child',0));
		
		$this->assign('rules',Db::name('member_auth_group')->where(array('id'=>input('id')))->field('rules')->find());
		
		return $this->fetch();
	}
	
	 public function write_group(){
	 	$data=input('post.');
		
		$group_id=$data['id'];
		
		Db::name('member_auth_rule')->where(array('group_id'=>$group_id))->delete();			
		
		
	 	if(isset($data['rules'])){
            sort($data['rules']);
            $data['rules']  = implode( ',' , array_unique($data['rules']));
        }
		
		Db::name('member_auth_group')->update($data,false,true);		
		
		$group_menu=Db::name('member_auth_group')->where('id',$group_id)->find();			

		$menu_id=explode(',', $group_menu['rules']);	
		
		$menu_list=Db::name('member_menu')->select();
		
		foreach ($menu_id as $k => $v) {
			
			foreach ($menu_list as $k1 => $v1) {
				
				if($v==$v1['id']){
					$m['group_id']=$group_id;
					$m['menu_id']=$v;					
					$m['name']=!empty($v1['url'])?$v1['url']:'';
					Db::name('member_auth_rule')->insert($m);
				}
				
			}					
			
		}
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'更新了会员用户组');	
		return $this->success('编辑成功',url('AuthBackend/index'));		

	 }
	public function set_status(){
		$data=input('param.');
		
		Db::name('member_auth_group')->where('id',$data['id'])->update(['status'=>$data['status']],false,true);		
		
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'修改了会员用户组状态');	
		
		$this->redirect('AuthBackend/index');
	}
}
?>