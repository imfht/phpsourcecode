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
 
namespace osc\mobile\controller;
use osc\common\controller\AdminBase;
use think\Db;
class AgentBackend extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','代理分销');	
	}
	
	public function index(){
		$this->assign('list',Db::name('AgentApply')->where('status',0)->paginate(config('page_num')));
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		return $this->fetch();
	}
	//审核
 	public function pass(){
 		
		if(request()->isPost()){
				
			$data=input('post.');
			//通过
			if($data['status']==1){
				
				Db::name('agent_apply')->where('aa_id',$data['aa_id'])->update(['status'=>1,'deal_time'=>time()]);			
				
				$aa=Db::name('agent_apply')->find($data['aa_id']);
				
				$member['is_agent']=1;
				$member['uid']=$aa['uid'];
						
				Db::name('member')->update($member);
				
				$agent['uid']=$aa['uid'];
				$agent['name']=$aa['name'];
				$agent['tel']=$aa['tel'];
				$agent['email']=$aa['email'];
				$agent['id_card']=$aa['id_cart'];
				$agent['agent_level']=$data['agent_level'];	
				$agent['return_percent']=get_agent_level_info($data['agent_level'],'return_percent');
				$agent['create_time']=time();
				
				Db::name('agent')->insert($agent);
						
			//未通过	
			}elseif($data['status']==2){
				$apply['aa_id']=$data['aa_id'];
				$apply['deal_time']=time();
				$apply['status']=$data['status'];
				$apply['answer']=$data['answer'];
				
				Db::name('agent_apply')->update($apply);
			}
			storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'审核代理');	
			$this->redirect('AgentBackend/agent_list');
		}
		$this->assign('level',Db::name('AgentLevel')->select());
		return $this->fetch();
	}
	
	public function level(){
		$this->assign('list',Db::name('AgentLevel')->paginate(config('page_num')));
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		return $this->fetch('agent_level:index');
	}
	
	function add_level(){
		if(request()->isPost()){	
			return $this->single_table_insert('AgentLevel','添加了代理级别');
		}
		$this->assign('action',url('AgentBackend/add_level'));
		$this->assign('crumb','新增');
		return $this->fetch('agent_level:edit');
	}
	public	function edit_level(){
		if(request()->isPost()){	
			return $this->single_table_update('AgentLevel','修改了代理级别');
		}
		$this->assign('crumb', '修改');
		$this->assign('action', url('AgentBackend/edit_level'));		
		$this->assign('d',Db::name('AgentLevel')->find(input('id')));		
		return $this->fetch('agent_level:edit');
	}
	public	function del_level(){
		if(request()->isGet()){	
			$r= $this->single_table_delete('AgentLevel','删除了代理级别');
			if($r){
				$this->redirect('AgentBackend/level');
			}
		}
	}
	
	public function agent_list(){
		
		$list=array();
		
		$list=Db::name('agent')->paginate(config('page_num'));
		
		$data=$list->toArray();
		
		foreach ($data['data'] as $k => $v) {
			
			$data['data'][$k]['member']=Db::name('member')->where('pid',$v['uid'])->count();
			$data['data'][$k]['order']=Db::name('agent_bonus')->where('agent_id',$v['agent_id'])->count();
			$data['data'][$k]['month']=Db::name('agent_bonus')->where(['order_status_id'=>config('paid_order_status_id'),'month_time'=>date('m',time()),'uid'=>$v['uid']])->sum('bonus');;
			
		}
		
		$this->assign('page',$list);
		
		$this->assign('list',$data['data']);
		
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		
		return $this->fetch();
	}
	
	public function edit_agent(){
		
		if(request()->isPost()){	
			$data=input('post.');		
			
			$data['return_percent']=get_agent_level_info($data['agent_level'],'return_percent');
			
			if(Db::name('agent')->update($data,false,true)){
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'编辑了代理信息');				
				return ['success'=>'编辑成功','action'=>'edit'];
			}else{
				return ['error'=>'编辑失败'];
			}
		}
		
		$this->assign('agent',Db::name('agent')->find(input('param.id')));
		$this->assign('level',Db::name('AgentLevel')->select());
		
		return $this->fetch('edit');
	}
	public function sub_member(){
		
		$this->assign('list',Db::name('member')->where('pid',(int)input('param.uid'))->paginate(config('page_num')));
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		return $this->fetch();
	}
	public function sub_order(){
		
		$this->assign('list',Db::name('agent_bonus')->where('uid',(int)input('param.uid'))->paginate(config('page_num')));
		
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		return $this->fetch();
	}
	public function share(){
					
		$list=Db::name('wechat_share')
			->alias('ws')
			->join('member m','m.uid = ws.uid','left')			
			->field('ws.*,m.nickname')
			->paginate(config('page_num'));	
		
		$this->assign('list',$list);
		
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
			
		return $this->fetch();
	}
}
?>