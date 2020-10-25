<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Admin\Model;
class StatisticsModel{
	
	function show_visitors_ip($date=''){
		
		$sql='SELECT * FROM '.C('DB_PREFIX').'visitors_ip';
		
		if(!empty($date)){
			$sql.=" where last_visit_time='".$date."'";
		}		
		
		$count=count(M()->query($sql));
		
		$Page = new \Think\Page($count,C('BACK_PAGE_NUM'));
		
		$show  = $Page->show();// 分页显示输出	
		
		$sql.=' order by vi_id desc LIMIT '.$Page->firstRow.','.$Page->listRows;
		
		$list=M()->query($sql);
				
		return array(
			'count'=>$count,
			'empty'=>'<tr><td colspan="20">~~暂无数据</td></tr>',
			'list'=>$list,
			'page'=>$show
		);	
		
	}
	
	//取得所有访问IP量
	function get_all_visitors_ip(){
		return M()->query('SELECT DISTINCT ip from '.C('DB_PREFIX').'visitors_ip');
	}
	//取某一天访问IP量
	function get_visitors_ip_by_date($date){		
		return M()->query("SELECT DISTINCT ip from ".C('DB_PREFIX')."visitors_ip where last_visit_time='".$date."'");
	}	
	//取得所有会员资料
	function get_all_member(){
		return M('member')->select();
	}
	//今日注册会员
	function get_today_register_member(){
		//时间大于零点时间戳
		return M()->query("SELECT * from ".C('DB_PREFIX')."member where create_time>=".strtotime(date('Y-m-d')));
	}
	public function get_total_sales($data=array()) {
			
		$sql = "SELECT SUM(total) AS total FROM " . C('DB_PREFIX') . "order WHERE order_status_id NOT IN (".C('default_order_status_id').",".C('cancel_order_status_id').")";

		if (!empty($data['date_added'])) {
			$sql .= " AND date_added>=".strtotime(date($data['date_added']))." AND date_added<=".(strtotime(date($data['date_added']))+86400);
		}
		
		$total=M()->query($sql);
		
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
	
	public function get_total_order($data=array()) {
			
		$sql = "SELECT count(*) AS total FROM " . C('DB_PREFIX') . "order ";

		if (!empty($data['date_added'])) {
			$sql .= " where date_added>=".strtotime(date($data['date_added']))." AND date_added<=".(strtotime(date($data['date_added']))+86400);
		}
		
		$total=M()->query($sql);
		
		
		return $total[0]['total'];
	}
	
	function get_user_action(){
		return M('user_action')->order('ua_id desc')->limit(C('BACK_PAGE_NUM'))->select();
	}
	
}
?>