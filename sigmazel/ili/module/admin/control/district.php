<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_log;
use admin\model\_district;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//地区
class district{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_district = new _district();
		
		$wheresql = ' AND PARENTID = 0';
		
		$_var['gp_parentid'] = $_var['gp_parentid'] + 0;
		if($_var['gp_parentid'] > 0) {
			$parent = $_district->get_by_id($_var['gp_parentid']);
			$_var['gp_parentid'] = $parent ? $parent['DISTRICTID'] : 0;
			if($_var['gp_parentid']) {
				$crumbs = $_district->get_crumbs($_var['gp_parentid']);
				$wheresql = " AND PARENTID = '{$_var[gp_parentid]}'";
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$district_names = '';
			$district_count = 0;
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$district = $_district->get_by_id($val);
				if(!$district) continue;
				
				$_district->delete($district['DISTRICTID']);
				
				$district_count++;
				$district_names .= $district['CNAME'].',';
			}
			
			if($parent){
				$parent['CHILDREN'] = $parent['CHILDREN'] - $district_count;
				$_district->update($parent['DISTRICTID'], array('CHILDREN' => $parent['CHILDREN']));
			}
			
			$_log->insert($GLOBALS['lang']['admin.district.log.delete']."({$district_names})", $GLOBALS['lang']['admin.district']);
		}
		
		if(is_array($_var['gp_newcname'])){
			foreach ($_var['gp_newcname'] as $key => $val){
				if($_var['gp_newcname'][$key]){
					$districtid = $_district->insert(array(
					'PARENTID' => $_var['gp_parentid'],
					'DISPLAYORDER' => $_var['gp_newdisplayorder'][$key] + 0,
					'CNAME' => utf8substr($_var['gp_newcname'][$key], 0, 30),
					'IDENTITY' => utf8substr($_var['gp_newidentity'][$key], 0, 30)
					));
					
					$_district->update($districtid, array('PATH' => ($parent ? $parent['PATH'].','.$districtid.',' : ','.$districtid.',')));
					
					if($parent){
						$parent['CHILDREN'] = $parent['CHILDREN'] + 1;
						$_district->update($parent['DISTRICTID'], array('CHILDREN' => $parent['CHILDREN']));
					}
					
					unset($districtid);
				}
			}
			
			$_log->insert($GLOBALS['lang']['admin.district.log.add'], $GLOBALS['lang']['admin.district']);
		}
		
		if(is_array($_var['gp_cname'])){
			foreach ($_var['gp_cname'] as $key => $val){
				$_district->update($key, array(
				'ENABLED' => $_var[gp_enabled][$key] + 0, 
				'DISPLAYORDER' => $_var[gp_displayorder][$key], 
				'CNAME' => $_var[gp_cname][$key], 
				'IDENTITY' => $_var[gp_identity][$key]
				));
			}
		}
		
		$count = $_district->get_count($wheresql);
		if($count){
			$districts = $_district->get_list($wheresql);
		}
		
		include_once view('/module/admin/view/district');
	}
	
}
?>