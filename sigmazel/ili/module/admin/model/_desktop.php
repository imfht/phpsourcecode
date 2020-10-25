<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

/**
 * 桌面
 * @author sigmazel
 * @since v1.0.2
 */
class _desktop{
	//获取mysql数据库版本
	public function get_mysqls(){
		global $db;
		
		$mysqls = $db->fetch_first("SHOW VARIABLES LIKE 'version'");
		$mysqls && $mysqls = explode('-', $mysqls['Value']);
		
		return $mysqls;
	}
	
	//获取桌面菜单列表
	public function get_menus($user){
		global $db;
		
		$_menu = new _menu();
		
		//取得桌面菜单
		$menus = array();
		$menuids = array();
		
		$menu_list = array();
		
		$menu_list['order_audit'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/order/audit');
		$menu_list['cms_comment'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/cms/comment');
		$menu_list['product_comment'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/product/comment');
		$menu_list['note_record'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/note/record');
		$menu_list['order_complaint'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/order/complaint');
		
		$menu_list['cms_articles'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/cms/article');
		$menu_list['product_list'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/product');
		$menu_list['order_list'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/order');
		
		$menu_list['product_main'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/product/main');
		$menu_list['order_main'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/order/main');
		$menu_list['user_main'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/user/main');
		
		foreach($menu_list as $key => $menu){
			$menuids[] = $menu['MENUID'];
		}
		
		//当前用户菜单列表
		if($user['USERID'] == -1) $menus = $menu_list;
		else{
			$temp_query = $db->query("SELECT m.* FROM tbl_menu m ,tbl_role_menu rm WHERE m.MENUID = rm.MENUID AND rm.ROLEID = '{$user[ROLEID]}' AND m.MENUID IN(".eimplode($menuids).") ORDER BY m.DISPLAYORDER ASC");
			while(($row = $db->fetch_array($temp_query)) !== false){
				foreach($menu_list as $key => $menu){
					if($menu['MENUID'] == $row['MENUID']){
						$menus[$key] = $menu;
						break;
					}
				}
			}
		}
		
		return $menus;
	}
	
	//获取用户待办事项
	public function get_task($menus){
		global $db;
		
		$task = array('ORDER_DEAL' => 0, 'COMMENT_PRODUCT' => 0, 'COMPLAINT_RECORD' => 0, 'COMMENT_ARTICLE' => 0, 'NOTE_RECORD' => 0);
		
		if($menus['order_audit']) $task['ORDER_DEAL'] = $db->result_first("SELECT COUNT(1) FROM tbl_order WHERE STATUS  = 0") + 0;
		if($menus['product_comment']) $task['COMMENT_PRODUCT'] = $db->result_first("SELECT COUNT(1) FROM tbl_comment WHERE ABOUTTYPE = 'product' AND PARENTID = 0 AND ISAUDIT = 0") + 0;
		if($menus['order_complaint']) $task['COMPLAINT_RECORD'] = $db->result_first("SELECT COUNT(1) FROM tbl_complaint WHERE STATUS = 0") + 0;
		
		if($menus['cms_comment']) $task['COMMENT_ARTICLE'] = $db->result_first("SELECT COUNT(1) FROM tbl_comment WHERE ABOUTTYPE = 'article' AND PARENTID = 0 AND LENGTH(REPLY) = 0") + 0;
		if($menus['note_record']) $task['NOTE_RECORD'] = $db->result_first("SELECT COUNT(1) FROM tbl_note_record WHERE PARENTID = 0 AND LENGTH(REPLY) = 0") + 0;
		
		
		return $task;
	}
	
	//获取数据统计
	public function get_data_stat($menus){
		global $db;
		
		$nowdate = date('Y-m-d');
		$prevdate = date('Y-m-d', strtotime("-1 days {$nowdate}"));
		
		$nowmonth = date('Y-m').'-01';
		$prevmonth = date('Y-m-d', strtotime("-1 months {$nowmonth}"));
		
		$stat = array('ALL' => array(), 'SALE' => array());
		
		$stat['ALL']['ARTICLE']['NOW'] = $db->result_first("SELECT COUNT(1) FROM tbl_article WHERE DATE_FORMAT(PUBDATE, '%Y-%m-%d') = '{$nowdate}' AND ISAUDIT <> -1") + 0;
		$stat['ALL']['ARTICLE']['PREV'] = $db->result_first("SELECT COUNT(1) FROM tbl_article WHERE DATE_FORMAT(PUBDATE, '%Y-%m-%d') = '{$prevdate}' AND ISAUDIT <> -1") + 0;
		$stat['ALL']['ARTICLE']['MONTH'] = $db->result_first("SELECT COUNT(1) FROM tbl_article WHERE DATE_FORMAT(PUBDATE, '%Y-%m-%d') >= '{$nowmonth}' AND ISAUDIT <> -1") + 0;
		$stat['ALL']['ARTICLE']['PREVMONTH'] = $db->result_first("SELECT COUNT(1) FROM tbl_article WHERE DATE_FORMAT(PUBDATE, '%Y-%m-%d') >= '{$prevmonth}' AND DATE_FORMAT(PUBDATE, '%Y-%m-%d') < '{$nowmonth}' AND ISAUDIT <> -1") + 0;
		$stat['ALL']['ARTICLE']['ALL'] = $db->result_first("SELECT COUNT(1) FROM tbl_article WHERE ISAUDIT <> -1") + 0;
		
		if($menus['product_main']){
			$stat['ALL']['PRODUCT']['NOW'] = $db->result_first("SELECT COUNT(1) FROM tbl_product WHERE PUBDATE = '{$nowdate}' AND ISAUDIT <> -1") + 0;
			$stat['ALL']['PRODUCT']['PREV'] = $db->result_first("SELECT COUNT(1) FROM tbl_product WHERE PUBDATE = '{$prevdate}' AND ISAUDIT <> -1") + 0;
			$stat['ALL']['PRODUCT']['MONTH'] = $db->result_first("SELECT COUNT(1) FROM tbl_product WHERE PUBDATE >= '{$nowmonth}' AND ISAUDIT <> -1") + 0;
			$stat['ALL']['PRODUCT']['PREVMONTH'] = $db->result_first("SELECT COUNT(1) FROM tbl_product WHERE PUBDATE >= '{$prevmonth}' AND PUBDATE < '{$nowmonth}' AND ISAUDIT <> -1") + 0;
			$stat['ALL']['PRODUCT']['ALL'] = $db->result_first("SELECT COUNT(1) FROM tbl_product WHERE ISAUDIT <> -1") + 0;
		}
		
		if($menus['cms_comment']){
			$stat['ALL']['COMMENT']['NOW'] = $db->result_first("SELECT COUNT(1) FROM tbl_comment WHERE ABOUTTYPE = 'article' AND PARENTID = 0 AND DATE_FORMAT(CREATETIME, '%Y-%m-%d') = '{$nowdate}'") + 0;
			$stat['ALL']['COMMENT']['PREV'] = $db->result_first("SELECT COUNT(1) FROM tbl_comment WHERE ABOUTTYPE = 'article' AND PARENTID = 0 AND DATE_FORMAT(CREATETIME, '%Y-%m-%d') = '{$prevdate}'") + 0;
			$stat['ALL']['COMMENT']['MONTH'] = $db->result_first("SELECT COUNT(1) FROM tbl_comment WHERE ABOUTTYPE = 'article' AND PARENTID = 0 AND DATE_FORMAT(CREATETIME, '%Y-%m-%d') >= '{$nowmonth}'") + 0;
			$stat['ALL']['COMMENT']['PREVMONTH'] = $db->result_first("SELECT COUNT(1) FROM tbl_comment WHERE ABOUTTYPE = 'article' AND PARENTID = 0 AND DATE_FORMAT(CREATETIME, '%Y-%m-%d') >= '{$prevmonth}' AND DATE_FORMAT(CREATETIME, '%Y-%m-%d') < '{$nowmonth}'") + 0;
			$stat['ALL']['COMMENT']['ALL'] = $db->result_first("SELECT COUNT(1) FROM tbl_comment WHERE ABOUTTYPE = 'article' AND PARENTID = 0") + 0;
		}
		
		if($menus['order_main']){
			$stat['ALL']['ORDER']['NOW'] = $db->result_first("SELECT COUNT(1) FROM tbl_order WHERE CREATETIME = '{$nowdate}' AND STATUS <> -1") + 0;
			$stat['ALL']['ORDER']['PREV'] = $db->result_first("SELECT COUNT(1) FROM tbl_order WHERE CREATETIME = '{$prevdate}' AND STATUS <> -1") + 0;
			$stat['ALL']['ORDER']['MONTH'] = $db->result_first("SELECT COUNT(1) FROM tbl_order WHERE CREATETIME >= '{$nowmonth}' AND STATUS <> -1") + 0;
			$stat['ALL']['ORDER']['PREVMONTH'] = $db->result_first("SELECT COUNT(1) FROM tbl_order WHERE CREATETIME >= '{$prevmonth}' AND CREATETIME < '{$nowmonth}' AND STATUS <> -1") + 0;
			$stat['ALL']['ORDER']['ALL'] = $db->result_first("SELECT COUNT(1) FROM tbl_order WHERE STATUS <> -1") + 0;
		}
		
		$stat['SALE']['VIEWLOG']['NOW'] = $db->result_first("SELECT SUM(VIEWS) FROM tbl_view_hour WHERE DATELINE = '{$nowdate}'") + 0;
		$stat['SALE']['VIEWLOG']['PREV'] = $db->result_first("SELECT SUM(VIEWS) FROM tbl_view_hour WHERE DATELINE = '{$prevdate}'") + 0;
		$stat['SALE']['VIEWLOG']['MONTH'] = $db->result_first("SELECT SUM(VIEWS) FROM tbl_view_hour WHERE DATELINE >= '{$nowmonth}'") + 0;
		$stat['SALE']['VIEWLOG']['PREVMONTH'] = $db->result_first("SELECT SUM(VIEWS) FROM tbl_view_hour WHERE DATELINE >= '{$prevmonth}' AND DATELINE < '{$nowmonth}'") + 0;
		$stat['SALE']['VIEWLOG']['ALL'] = $db->result_first("SELECT SUM(VIEWS) FROM tbl_view_hour") + 0;
		
		if($menus['user_main']){
			$stat['SALE']['USER']['NOW'] = $db->result_first("SELECT COUNT(1) FROM tbl_user WHERE CREATETIME = '{$nowdate}' AND ISMANAGER = 0") + 0;
			$stat['SALE']['USER']['PREV'] = $db->result_first("SELECT COUNT(1) FROM tbl_user WHERE CREATETIME = '{$prevdate}' AND ISMANAGER = 0") + 0;
			$stat['SALE']['USER']['MONTH'] = $db->result_first("SELECT COUNT(1) FROM tbl_user WHERE CREATETIME >= '{$nowmonth}' AND ISMANAGER = 0") + 0;
			$stat['SALE']['USER']['PREVMONTH'] = $db->result_first("SELECT COUNT(1) FROM tbl_user WHERE CREATETIME >= '{$prevmonth}' AND CREATETIME < '{$nowmonth}' AND ISMANAGER = 0") + 0;
			$stat['SALE']['USER']['ALL'] = $db->result_first("SELECT COUNT(1) FROM tbl_user WHERE ISMANAGER = 0") + 0;
		}
		
		if($menus['order_main']){
			$stat['SALE']['BUYER']['NOW'] = $db->result_first("SELECT COUNT(1) FROM (SELECT USERID, COUNT(1) FROM tbl_order WHERE USERID > 0 AND CREATETIME = '{$nowdate}' GROUP BY USERID) AS temp") + 0;
			$stat['SALE']['BUYER']['PREV'] = $db->result_first("SELECT COUNT(1) FROM (SELECT USERID, COUNT(1) FROM tbl_order WHERE USERID > 0 AND CREATETIME = '{$prevdate}' GROUP BY USERID) AS temp") + 0;
			$stat['SALE']['BUYER']['MONTH'] = $db->result_first("SELECT COUNT(1) FROM (SELECT USERID, COUNT(1) FROM tbl_order WHERE USERID > 0 AND CREATETIME >= '{$nowmonth}' GROUP BY USERID) AS temp") + 0;
			$stat['SALE']['BUYER']['PREVMONTH'] = $db->result_first("SELECT COUNT(1) FROM (SELECT USERID, COUNT(1) FROM tbl_order WHERE USERID > 0 AND CREATETIME >= '{$prevmonth}' AND CREATETIME < '{$nowmonth}' GROUP BY USERID) AS temp") + 0;
			$stat['SALE']['BUYER']['ALL'] = $db->result_first("SELECT COUNT(1) FROM (SELECT USERID, COUNT(1) FROM tbl_order WHERE USERID > 0 GROUP BY USERID) AS temp") + 0;
		}
		
		return $stat;
	}
	
	//获取数据更新
	public function get_data_updated($menus){
		global $db;
		
		$datas = array();
		
		if($menus['cms_articles']){
			$temp_query = $db->query("SELECT ARTICLEID AS ID, TITLE, PUBDATE, SUMMARY FROM tbl_article WHERE ISAUDIT <> -1 ORDER BY PUBDATE DESC LIMIT 0, 10");
			while(($row = $db->fetch_array($temp_query)) !== false){
				$row['TIMER'] = strtotime($row['PUBDATE']);
				$row['PUBDATE'] = subtimer(strtotime($row['PUBDATE']));
				$row['TYPE'] = 'article';
				$datas[]  = $row;
			}
		}
		
		if($menus['product_list']){
			$temp_query = $db->query("SELECT PRODUCTID AS ID, TITLE, PUBDATE, INTRODUCTION FROM tbl_product WHERE ISAUDIT <> -1 ORDER BY PUBDATE DESC LIMIT 0, 10");
			while(($row = $db->fetch_array($temp_query)) !== false){
				$row['TIMER'] = strtotime($row['PUBDATE']);
				$row['PUBDATE'] = subtimer(strtotime($row['PUBDATE']));
				$row['TYPE'] = 'product';
				$datas[]  = $row;
			}
		}
		
		if($menus['order_list']){
			$district_ids = array();
			
			$temp_query = $db->query("SELECT ORDERID AS ID, TOTALPRICE, CREATETIME, PROVINCEID, CITYID, COUNTYID, PLACE, CONSIGNEE FROM tbl_order WHERE STATUS <> -1 ORDER BY CREATETIME DESC LIMIT 0, 10");
			while(($row = $db->fetch_array($temp_query)) !== false){
				if(!in_array($row['PROVINCEID'], $district_ids)) $district_ids[] = $row['PROVINCEID'];
				if(!in_array($row['CITYID'], $district_ids)) $district_ids[] = $row['CITYID'];
				if(!in_array($row['COUNTYID'], $district_ids)) $district_ids[] = $row['COUNTYID'];
				
				$row['TIMER'] = strtotime($row['CREATETIME']);
				$row['CREATETIME'] = subtimer(strtotime($row['CREATETIME']));
				$row['TYPE'] = 'order';
				$datas[]  = $row;
			}
			
			$district_list = array();
			$temp_query = $db->query("SELECT * FROM tbl_district WHERE DISTRICTID IN(".eimplode($district_ids).")");
			while(($row = $db->fetch_array($temp_query)) !== false){
				$district_list[$row['DISTRICTID']] = $row;
			}
			
			foreach ($datas as $key => $data){
				if($data['TYPE'] == 'order'){
					if($district_list[$data['COUNTYID']]) $data['PLACE'] = $district_list[$data['COUNTYID']]['CNAME'].'-'.$data['PLACE'];
					if($district_list[$data['CITYID']]) $data['PLACE'] = $district_list[$data['CITYID']]['CNAME'].'-'.$data['PLACE'];
					if($district_list[$data['PROVINCEID']]) $data['PLACE'] = $district_list[$data['PROVINCEID']]['CNAME'].'-'.$data['PLACE'];
					
					$data['TITLE'] = $data['CONSIGNEE'].' '.$data['PLACE'];
					$datas[$key] = $data;
				}
			}
		}
		
		usort($datas, "_sort_data");
		
		return $datas;
	}
	
}

function _sort_data($a, $b){
	if ($a['TIMER'] == $b['TIMER']) return 0;

	return ($a['TIMER'] > $b['TIMER']) ? -1 : 1;
}
?>