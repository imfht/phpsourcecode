<?php
namespace app\admin\controller;
use app\admin\model\AuthRule;
use app\admin\model\AuthGroup;

/**
 * 授权操作,部分代码改编于onethink
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
class Author extends Base
{	

	/**
	 * 访问授权 menu
	 */
    public function access(){
		$auth_rule = model('AuthRule');
		$auth_rule->updateRules();
		$group_id = input('group_id');
		$type = 'access';
		if(request()->isPost()){
			$data = input();
			if(isset($data['rules'])){
				sort($data['rules']);
				$data['rules']  = implode( ',' , array_unique($data['rules']));
			}
			$data['module'] = 'admin';
			$data['type'] =  AuthGroup::TYPE_ADMIN;
			$data['id']	= $group_id;
			unset($data['group_id']);
			$auth_group =  model('AuthGroup')->editGroup($data);
			if($auth_group){
				$this->success('操作成功',url('auth_group/index'));
			}else{
				$this->error('操作失败');
			}
			
		}else{
			$menu = model('Menu');
			$group_role = model('AuthGroup')->getFindOne($group_id);
			$main_rules = $auth_rule->mainRules(['module'=>'admin','type'=>AuthRule::RULE_MAIN,'status'=>1]);
			$child_rules =  $auth_rule->mainRules(['module'=>'admin','type'=>AuthRule::RULE_URL,'status'=>1]);
			$node_list = $menu->returnNodes();
			$this->assign('main_rules',$main_rules);
			$this->assign('auth_rules',$child_rules);
			$this->assign('node_list', $node_list);
			$this->assign('type',$type);
			$this->assign('group_id',$group_id);
			$this->assign('group_role',$group_role);
			return $this->fetch();
		}
		
    }
	
	/**
	 * 用户授权列表
	 */
	public function member(){
		$group_id = input('group_id');
		$type = 'member';
		$group_role = model('AuthGroup')->getFindOne($group_id);
		$list = model('AuthGroupAccess')->getGroupsMember($group_id);
		$data = [];
		foreach($list as $k=>$v){
			$admin = get_admin_info($v['uid']);
			$group = get_group_info($v['group_id']);
			$data[$k]['uid'] = $v['uid'];
			$data[$k]['username'] = $admin['username'];
			$data[$k]['nickname'] = $admin['nickname'];
			$data[$k]['phone'] = $admin['phone'];
			$data[$k]['email'] = $admin['email'];
			$data[$k]['status'] = $admin['status'];
			$data[$k]['group_name'] = $group['title'];
			$data[$k]['group_id'] = $v['group_id'];
		}
		$this->assign('type',$type);
		$this->assign('group_id',$group_id);
		$this->assign('group_role',$group_role);
		$this->assign('list',$data);
		return $this->fetch('access');
	}
	
	/**
	 * 取消授权
	 */
	public function cleanbind(){
		$param = input();
		if($param['uid'] && $param['group_id']){
			$map = [
				'uid'		=> $param['uid'],
				'group_id'	=> $param['group_id'],
			];
			if(model('AuthGroupAccess')->removeBind($map)){
				$this->success('撤销成功');
			}else{
				$this->error('撤销失败');
			}
		}else{
			$this->error('撤销失败');
		}
	}
	
}
