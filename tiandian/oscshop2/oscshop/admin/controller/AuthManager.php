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
class AuthManager extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','系统');
		$this->assign('breadcrumb2','权限管理');
	}
	
     public function index(){
     	
     	$list = Db::name('auth_group')->paginate(config('page_num'));
		
		$this->assign('list',$list);

    	return $this->fetch();
	 }
	
	public function create_group(){		

		if(request()->isPost()){			  
			return $this->single_table_insert('AuthGroup','添加了用户组');
		}
		
		$this->assign('breadcrumb2','新增');
		$this->assign('action',url('AuthManager/create_group'));
		return $this->fetch('edit_group');
		
	}
	
	function edit_group(){
		if(request()->isPost()){
			return $this->single_table_update('AuthGroup','修改了用户组');
		}	
		
		$this->assign('group',Db::name('auth_group')->find((int)input('id')));
		$this->assign('breadcrumb2','编辑');
		$this->assign('action',url('AuthManager/edit_group'));
		return $this->fetch('edit_group');		
	}
	
	public function del_group(){
		
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除了用户组');		
			
		Db::name('auth_group')->delete((int)input('id'));
		
		$this->redirect('AuthManager/index');		
	}
	
	public function access(){
		
		$menu=Db::name('Menu')->order('sort_order')->select();				
		
		$this->assign('access_menu',list_to_tree($menu,'id','pid','child',0));
		
		$this->assign('rules',Db::name('auth_group')->where(array('id'=>input('id')))->field('rules')->find());
		
		return $this->fetch();
	}
	
	 public function write_group(){
	 	if(request()->isPost()){
	 	
	 	$data=input('post.');
		
		$group_id=(int)$data['id'];
		
		Db::name('auth_rule')->where(array('group_id'=>$group_id))->delete();
		
	 	if(isset($data['rules'])){
            sort($data['rules']);
            $data['rules']  = implode( ',' , array_unique($data['rules']));
        }
		
		Db::name('auth_group')->update($data,false,true);
		
		$group_menu=Db::name('auth_group')->where('id',$group_id)->find();			

		$menu_id=explode(',', $group_menu['rules']);	
		
		$menu_list=Db::name('menu')->select();
		
		foreach ($menu_id as $k => $v) {
			
			foreach ($menu_list as $k1 => $v1) {
				
				if($v==$v1['id']){
					$m['group_id']=$group_id;
					$m['menu_id']=$v;					
					$m['name']=!empty($v1['url'])?$v1['url']:'';
					Db::name('auth_rule')->insert($m);
				}
				
			}					
			
		}
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'编辑了用户权限');	
		
		return $this->success('编辑成功',url('AuthManager/index'));
		
		}
	 }
	public function set_status(){
		$data=input('param.');
		
		Db::name('auth_group')->where('id',$data['id'])->update(['status'=>(int)$data['status']],false,true);		
		
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'修改了用户组状态');	
		
		$this->redirect('AuthManager/index');
	}
}
?>