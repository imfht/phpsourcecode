<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
namespace admin\Controller;
use Think\Controller;
use Think\Exception;
class IndexController extends BaseController {
    public function index(){
    	try {

    		


	    	//获取路由信息
	    	$router = DD('Router');
	    	$router_info = $router->get_router_info_for_admin();
	    	
	    	

	    	//统计该路由历史用户总数
			$signinlog = DD('UserSigninLog');
			//历史最高在线人数
			$max_log = $signinlog->get_signlog_for_max_by_mid();
			


			//最近10天的认证数流量图
			$log_list_top10 = $signinlog->get_signlog_for_top10_by_mid();
			

			$today = date('Y-m-d');
			$yesterday = date('Y-m-d' , strtotime('-1 day')); 
			//定义最近10天的数组
			$temp_array = array(
				date('Y-m-d' , strtotime('-9 day')),
				date('Y-m-d' , strtotime('-8 day')),
				date('Y-m-d' , strtotime('-7 day')),
				date('Y-m-d' , strtotime('-6 day')),
				date('Y-m-d' , strtotime('-5 day')),
				date('Y-m-d' , strtotime('-4 day')),
				date('Y-m-d' , strtotime('-3 day')),
				date('Y-m-d' , strtotime('-2 day')),
				$yesterday, 
				$today, 
			);

			$temp = array();
			foreach ($log_list_top10 as $key => $value) {
				$temp[$value['date']] = $value;
			}
			$today = intval($temp[$today]['user_total']);
			$yesterday = intval($temp[$yesterday]['user_total']);
			$top10 = array();
			foreach ($temp_array as $key => $value) {
				$top10[] = array('date'=>$value, 'login_total'=>intval($temp[$value]['user_total']));
			}

			//累计认证人数
			$sum_log = $signinlog->get_signlog_for_sum_by_mid();
			
			//获取微站统计
			$merchants_micro_station_slide = D('MerchantsMicroStationSlide');
			$slide_num = $merchants_micro_station_slide->get_station_slide_count();

			$merchants_micro_station_new = D('MerchantsMicroStationNew');
			$new_num = $merchants_micro_station_new->get_station_new_count();
			
			$merchants_micro_station_product = D('MerchantsMicroStationProduct');
			$product_num = $merchants_micro_station_product->get_station_product_count();
			
			$merchants_micro_station_activity = D('MerchantsMicroStationActivity');
			$activity_num = $merchants_micro_station_activity->get_station_activity_count();

			//获取今日新老访客分布
			$client = D('Client');
			$new_old_count = $client->get_client_new_old_list_by_mid();
			
			//获取今日终端分布
			$device_type_count = $client->get_client_device_type_by_mid();

			$cop = C('COPYRIGHT');
			$return_array = array(
				'title'				=> $cop['pname_cn'].$cop['version_major'].'-首页',
				'router_info'		=> $router_info,
				'router'			=> array(
						'router_0'	=> $router_0,
						'router_1'	=> $router_1,
						'router_2'	=> $router_2,
						'router_3'	=> $router_3,
						'router_4'	=> $router_4,
				),
				
				'max_log'			=> $max_log,
				'today'				=> $today,
				'yesterday'			=> $yesterday,
				'top10'				=> json_encode($top10),
				'sum_log'			=> $sum_log,
				'station'			=> array(
						'slide_num'	=> $slide_num,
						'new_num'	=> $new_num,
						'product_num'=> $product_num,
						'activity_num'=> $activity_num,
				),
				'new_old_count'		=> $new_old_count,
				'device_type_count'	=> $device_type_count,

			);

			$this->assign($return_array);
			$this->display();
    	} catch (Exception $e) {

    		$this->error($e->getMessage(), U('Admin/login'));
    	}
    }
    /*
	* 版权信息
    */
    public function copyright(){
    	$cop = C('COPYRIGHT');
    	//获取商户信息
    	$merchant = DD('Merchant');
    	
    	$cop['web_site'] = C('WEB_SITE');
    	exit(json_encode($cop));
    }
    
}