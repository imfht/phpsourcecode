<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_log;
use cms\model\_category;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//导航
class nav{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_category = new _category();
		
		if($_var['gp_formsubmit']){
			foreach($_var['gp_cname'] as $key => $val){
				if(empty($val)) continue;
				
				if($_var['gp_navid'][$key]) {
					$navid = $_var['gp_navid'][$key];
					
					$_category->update($navid, array(
					'CNAME' => $val,
					'URL' => $_var['gp_url'][$key],
					'COMMENT' => $_var['gp_comment'][$key],
					'DISPLAYORDER' => $_var['gp_displayorder'][$key] + 0,
					'EDITER' => $_var['current']['USERNAME'],
					'EDITTIME' => date('Y-m-d H:i:s')
					));
				}else{
					$navid = $_category->insert(array(
					'CNAME' => $val, 
					'URL' => $_var['gp_url'][$key], 
					'COMMENT' => $_var['gp_comment'][$key], 
					'DISPLAYORDER' => $_var['gp_displayorder'][$key] + 0, 
					'EDITER' => $_var['current']['USERNAME'],
					'EDITTIME' => date('Y-m-d H:i:s'),
					'TYPE' => 'nav'
					));
					
					$_category->update($navid, array('PATH' => ",{$navid},"));
				}
				
				foreach($_var['gp_ccname'][$key] as $ckey => $cval){
					if(empty($cval)) continue;
					
					if($_var['gp_cnavid'][$key][$ckey]){
						$_category->update($_var['gp_cnavid'][$key][$ckey], array(
						'PARENTID' => $navid,
						'CNAME' => $cval,
						'URL' => $_var['gp_curl'][$key][$ckey],
						'COMMENT' => $_var['gp_ccomment'][$key][$ckey],
						'DISPLAYORDER' => $_var['gp_cdisplayorder'][$key][$ckey] + 0,
						'EDITER' => $_var['current']['USERNAME'],
						'EDITTIME' => date('Y-m-d H:i:s')
						));
					}else{
						$cnavid = $_category->insert(array(
						'PARENTID' => $navid, 
						'CNAME' => $cval,
						'URL' => $_var['gp_curl'][$key][$ckey],
						'COMMENT' => $_var['gp_ccomment'][$key][$ckey],
						'DISPLAYORDER' => $_var['gp_cdisplayorder'][$key][$ckey] + 0,
						'EDITER' => $_var['current']['USERNAME'],
						'EDITTIME' => date('Y-m-d H:i:s'),
						'TYPE' => 'nav'
						));
						
						$_category->update($cnavid, array('PATH' => ",{$navid},,{$cnavid},"));
					}
				}
				
				
				unset($navid);
				unset($cnavid);
			}
			
			cache_delete('nav');
		}
		
		if(is_array($_var['gp_cbxItem']) && $_var['gp_do'] == 'delete_list'){
			$nav_names = '';
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$nav = $_category->get_by_id($val);
				if($nav){
					$_category->delete($nav['CATEGORYID']);
				}
				
				unset($nav);
			}
			
			$_log->insert($GLOBALS['lang']['admin.nav.log.delete.list']."(".implode($_var[gp_cbxItem]).")", $GLOBALS['lang']['admin.nav']);
			
			cache_delete('nav');
		}
		
		$nav_list = $_category->get_tree(0, 'nav');
		
		include_once view('/module/admin/view/nav');
	}
	
}
?>