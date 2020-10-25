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
class Cash extends MobileBase
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
	
	//已提现
	function index(){

		$this->assign('list',Db::name('agent_cash_apply')->where(array('uid'=>UID))->select());
		$this->assign('empty',"<span style='margin:10px 0 0 20px;display:block;'>没有数据</span>");
		$this->assign('top_title','提现记录');
		return $this->fetch();
	}
	//未提现
	function no_cash(){
		
		if(request()->isPost()){
			
			$data=input('post.');
			//提现的额度
			if($data['cash']<config('cash_num')){				
				return ['error'=>'提现最小额度是'.config('cash_num').'元'];
			}
			if(!is_numeric($data['cash'])){				
				return ['error'=>'请输入数字'];
			}			
			//负数
			if($data['cash']<0){
				return ['error'=>'提现金额不能是负数'];
			}
			$agent=Db::name('agent')->where(array('agent_id'=>$data['agent_id']))->find();
			
			if($agent['no_cash']<$data['cash']){
				return ['error'=>'余额不足'];		
			}
			//从未提现金额中扣除			
			Db::name('agent')->where('agent_id',$data['agent_id'])->setDec('no_cash',$data['cash']);	
			Db::name('member')->where('uid',UID)->setDec('total_bonus',$data['cash']);			
			
			$data['create_time']=time();
			$data['uid']=UID;
			
			if(Db::name('agent_cash_apply')->insert($data,false,true)){
				storage_user_action(UID,user('nickname'),config('FRONTEND_USER'),'申请提现');			
				return ['success'=>'申请成功'];
			}else{
				return ['error'=>'申请失败'];				
			}
			
		}
		$this->assign('agent',Db::name('agent')->where(array('uid'=>UID))->find());
		return $this->fetch('apply');
		
	}
	
}	