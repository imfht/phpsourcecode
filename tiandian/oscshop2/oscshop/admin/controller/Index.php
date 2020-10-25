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
class Index extends AdminBase{
	
    public function index()
    {
    	//订单数量    
		$this->assign('total_order',$this->get_total_order());
		$this->assign('today_order',$this->get_total_order(array('date_added' => date('Y-m-d'))));	
    	//销售额
		$this->assign('total_money',$this->get_total_sales());
		$this->assign('today_money',$this->get_total_sales(array('date_added' => date('Y-m-d'))));
    	//用户行为 
    	$this->assign('user_action_total',count(Db::query("SELECT ua_id from ".config('database.prefix')."user_action")));
		$this->assign('today_user_action',count(Db::query("SELECT ua_id from ".config('database.prefix')."user_action where add_time>=".strtotime(date('Y-m-d')))));
		//会员
    	$this->assign('today_member',count(Db::query("SELECT uid from ".config('database.prefix')."member where regdate>=".strtotime(date('Y-m-d')))));
		$this->assign('member_count',Db::name('member')->count());		
		//用户行为列表
    	$this->assign('user_action',Db::name('user_action')->order('ua_id desc')->limit(config('page_num'))->select());
		$this->assign('uc_empty', '<tr><td colspan="20">~~暂无数据</td></tr>');		
		//订单列表	
		$this->assign('order_list',osc_order()->order_list(null,10));		
		$this->assign('empty', '<tr><td colspan="20">~~暂无数据</td></tr>');    
		
		return $this->fetch();   
    }

	//销售额
	public function get_total_sales($data=array()) {
			
		$sql = "SELECT SUM(total) AS total FROM " . config('database.prefix') . "order WHERE order_status_id NOT IN (".config('default_order_status_id').",".config('cancel_order_status_id').")";

		if (isset($data['date_added'])) {
			$sql .= " AND date_added>=".strtotime(date($data['date_added']))." AND date_added<=".(strtotime(date($data['date_added']))+86400);
		}
		
		$total=Db::query($sql);
		
		$sale_total=$total[0]['total'];
		
		if($sale_total){			
			
			if ($sale_total > 1000000000000) {
				$data = round($sale_total / 1000000000000, 1) . 'T';
			} elseif ($sale_total > 1000000000) {
				$data = round($sale_total / 1000000000, 1) . 'B';
			} elseif ($sale_total > 1000000) {
				$data = round($sale_total / 1000000, 1) . 'M';
			} elseif ($sale_total > 1000) {
				$data = round($sale_total / 1000, 1) . 'K';
			} else {
				$data = round($sale_total);
			}
		}else{
			
			return 0;
		}
		return $data;
	}
	//订单
	public function get_total_order($data=array()) {
			
		$sql = "SELECT count(*) AS total FROM " . config('database.prefix') . "order ";

		if (isset($data['date_added'])) {
			$sql .= " where date_added>=".strtotime(date($data['date_added']))." AND date_added<=".(strtotime(date($data['date_added']))+86400);
		}
		
		$total=Db::query($sql);
		
		
		return $total[0]['total'];
	}
		
	public function logout(){
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'退出了系统');
		osc_service('admin','user')->logout();		
		$this->redirect('Login/login');
	}
	
	public function clear(){
        clear_cache();
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'清除了缓存');
        return $this->success('缓存清理完毕');
    }
}
