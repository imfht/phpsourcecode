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
use think\Db;
class Agent extends MobileBase
{
	protected function _initialize(){
		parent::_initialize();						
		define('UID',osc_service('mobile','user')->is_login());	
		//手机版
		if(!UID){
			if(!in_wechat()){
				$this->redirect('login/login');	
			}else{
				$this->error('系统错误');
			}			
		}		
	}
	//申请成为代理
	function apply_agent(){
		
		if(request()->isPost()){
			
			$data=input('post.');
			
			$validate=new \osc\mobile\validate\Agent();		
					
			if(!$validate->check($data)){				
			    return ['error'=>$validate->getError()];				
			}
			
			$data['uid']=UID;
			$data['create_time']=time();
			
			if(Db::name('agent_apply')->insert($data,false,true)){
				storage_user_action(UID,user('nickname'),config('FRONTEND_USER'),'申请成为代理');
				return ['success'=>'申请成功，请等待审核'];
			}else{
				return ['error'=>'申请失败'];
			}
		}	
		
		$map['uid']=['eq',UID];
		$map['status']=['neq',1];
		
		$apply_restult=Db::name('agent_apply')->where($map)->order('aa_id desc')->limit(1)->find();
		
		if($apply_restult){			
			if($apply_restult['status']==0){
				return $this->fetch('already_apply');
			}
			$this->assign('apply_result',$apply_restult);	
		}
		
		
		$this->assign('SEO',['title'=>'代理申请-'.config('SITE_TITLE')]);	
		$this->assign('top_title','代理申请');
		return $this->fetch();
	}
	//编辑申请资料
	function edit_apply_agent(){
		if(request()->isPost()){
			
			$data=input('post.');
			$data['status']=0;
			if(Db::name('agent_apply')->update($data,false,true)){
				storage_user_action(UID,user('nickname'),config('FRONTEND_USER'),'编辑申请代理资料');
				return ['success'=>'修改成功，请等待审核'];
			}else{
				return ['error'=>'修改失败'];
			}
		}
	}
	//我的资料
	function my_agent_info(){
		
		if(request()->isPost()){
			$agent=input('post.');
			
			if(Db::name('agent')->update($agent,false,true)){
				storage_user_action(UID,user('nickname'),config('FRONTEND_USER'),'编辑我的资料');
				return ['success'=>'修改成功'];
			}else{
				return ['error'=>'修改失败'];
			}
			
		}
		$this->assign('agent',Db::name('agent')->where(array('uid'=>UID))->find());
		$this->assign('SEO',['title'=>'我的资料-'.config('SITE_TITLE')]);	
		$this->assign('top_title','我的资料');
		return $this->fetch();
	}
	function sub_agent(){		
		
		$this->assign('agent',Db::name('agent')->where(array('uid'=>UID))->find());
		
		$this->assign('today',Db::name('agent_bonus')->where([
		'order_status_id'=>config('paid_order_status_id'),
		'create_time'=>date('Y-m-d',time()),
		'uid'=>UID
		])->field('SUM(bonus) as total')->select());
		
		$this->assign('yesterday',Db::name('agent_bonus')->where([
		'order_status_id'=>config('paid_order_status_id'),
		'create_time'=>date('Y-m-d',strtotime("-1 day")),
		'uid'=>UID
		])->field('SUM(bonus) as total')->select());
		
		$this->assign('member',Db::name('member')->where(array('pid'=>UID))->count());
		
		$order=Db::name('agent_bonus')
			->alias('ab')				
			->join('member m','ab.buyer_id = m.uid','left')		
			->field('ab.*,m.nickname,m.userpic')	
			->where(array('ab.uid'=>UID))
			->order('ab.ab_id desc')
			->select();
	
		$this->assign('list',$order);
		$this->assign('empty',"<span style='margin-left:20px;'>没有订单数据</span>");
		$this->assign('top_title','我的代理');
		$this->assign('SEO',['title'=>'我的代理-'.config('SITE_TITLE')]);	
		return $this->fetch();
	}

	function order(){
		
		$type=input('param.type');
		
		if($type=='today'){
			$this->assign('top_title','今日订单');					
			$where=array('ab.order_status_id'=>config('paid_order_status_id'),'ab.create_time'=>date('Y-m-d',time()),'ab.uid'=>UID);			
		}elseif($type=='yesterday'){
			$this->assign('top_title','昨日订单');			
			$where=array('ab.order_status_id'=>config('paid_order_status_id'),'ab.create_time'=>date('Y-m-d',strtotime("-1 day")),'ab.uid'=>UID);			
		}elseif($type=='total'){
			$this->assign('top_title','全部订单');					
			$where=array('ab.order_status_id'=>config('paid_order_status_id'),'ab.uid'=>UID);
		}
		
		$list=Db::name('agent_bonus')
			->alias('ab')				
			->join('member m','ab.buyer_id = m.uid','left')		
			->field('ab.*,m.nickname,m.userpic')	
			->where($where)
			->order('ab.ab_id desc')
			->select();
		
		$this->assign('list',$list);
		
		$this->assign('empty',"<span style='margin:10px 0 0 20px;display:block;'>没有订单数据</span>");
		return $this->fetch();
	}
	
	function member(){

		$this->assign('empty',"<span style='margin:10px 0 0 20px;display:block;'>没有数据</span>");		
		$this->assign('top_title','名下会员');
		$this->assign('SEO',['title'=>'名下会员-'.config('SITE_TITLE')]);	
		return $this->fetch();
	}
	
	function ajax_member_list(){
		
		$page=input('param.page');//页码
        //开始数字,数据量
		$limit = (12 * $page) . ",12";
		
		$list=Db::name('member')->where(['pid'=>UID])->field('nickname,userpic,regdate')->order('uid desc')->limit($limit)->select();
		
		foreach ($list as $k => $v) {
			$list[$k]['create_time']=date('Y-m-d',$v['regdate']);
		}
		
		return $list;
	}
}
