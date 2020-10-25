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
class CashBackend extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','代理分销');	
	}
	//提现申请
	public function cash_apply(){
		$this->assign('list',Db::name('agent_cash_apply')->where('status',0)->paginate(config('page_num')));
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		return $this->fetch();
	}
	
	function pass_cash_apply(){
		
	 	$data=input('param.');
		
		Db::name('agent_cash_apply')->update(array('aca_id'=>(int)$data['id'],'status'=>1,'admin_user'=>UID,'pass_time'=>time()));
		//已经提现的		
		Db::name('agent')->where('agent_id',(int)$data['agent_id'])->setInc('cash',(float)$data['cash']);//增加
		Db::name('member')->where('uid',(int)$data['uid'])->setDec('total_bonus',(float)$data['cash']);//减少
		
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'通过了提现申请');	
		
		$this->redirect('CashBackend/cash_apply');
		
	 }
	//提现记录
	public function cash_record(){
		
		$this->assign('list',Db::name('agent_cash_apply')->where('status',1)->paginate(config('page_num')));
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		return $this->fetch();
	}
}
?>