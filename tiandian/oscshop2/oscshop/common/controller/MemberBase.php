<?php
/**
 *
 * @author    李梓钿
 *会员中心
 */
namespace osc\common\controller;
use think\Db;
class MemberBase extends Base{	
	
	protected function _initialize() {
		parent::_initialize();		
		
		define('UID',osc_service('member','user')->is_login());
		
		if(!UID){
	
			return $this->error('请先登录','/');
		}			
	
		$this->get_menu();

        //权限判断  	        
		$auth = new \auth\Auth('member_');
		
		$url=request()->module().'/'.to_under_score(request()->controller()).'/'.request()->action();
		
		if (!$auth->check($url,UID)) {
			$this->error('没有权限！');
		}

	}
	
	public function get_menu(){
		
		$map['MemberMenu.type']=['eq','nav'];
		$map['MemberAuthRule.group_id']=['eq',member('group_id')];
		
		$menu = Db::view('MemberAuthRule','menu_id')
		->view('MemberMenu','*','MemberAuthRule.menu_id=MemberMenu.id')		
		->where($map)	
		->order('MemberMenu.sort_order asc')
		->select();	

		$parent_menu=list_to_tree($menu,'id','pid','children',0);
		
		$this->assign('admin_menu',$parent_menu);
	
		
	}
	


	
}
