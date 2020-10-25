<?php
//版权所有(C) 2014 www.ilinei.com

namespace wx\control;

use admin\model\_log;
use wx\model\_wx;
use wx\model\_wx_menu;
use wx\model\_wx_setting;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/wx/lang.php';

//自定义菜单
class menu{
	//默认
	public function index(){
		global $_var, $setting;
		
		$_log = new _log();
		
		$_wx = new _wx();
		$_wx_menu = new _wx_menu();
		$_wx_setting = new _wx_setting();
		
		$wx_setting = $_wx_setting->get();
		
		if(!$wx_setting['WX_OPEN']) show_message($GLOBALS['lang']['wx.menu.message.open'], 0);
		elseif(!$wx_setting['WX_MENU']) show_message($GLOBALS['lang']['wx.menu.message.menu'], 0);
		
		if($_var['gp_formsubmit']){
			foreach($_var['gp_cname'] as $key => $val){
				if(empty($val)) continue;
				
				if($_var['gp_menuid'][$key]) {
					$menuid = $_var['gp_menuid'][$key];
					
					$_wx_menu->update($menuid, array(
					'NAME' => $val,
					'URL' => $_var['gp_url'][$key],
					'REMARK' => $_var['gp_remark'][$key],
					'DISPLAYORDER' => $_var['gp_displayorder'][$key] + 0,
					'USERID' => $_var['current']['USERID'],
					'USERNAME' => $_var['current']['USERNAME'],
					'EDITTIME' => date('Y-m-d H:i:s')
					));
				}else{
					$menuid = $_wx_menu->insert(array(
					'NAME' => $val, 
					'URL' => $_var['gp_url'][$key], 
					'REMARK' => $_var['gp_remark'][$key], 
					'DISPLAYORDER' => $_var['gp_displayorder'][$key] + 0, 
					'USERID' => $_var['current']['USERID'],
					'USERNAME' => $_var['current']['USERNAME'],
					'EDITTIME' => date('Y-m-d H:i:s')
					));
				}
				
				foreach($_var['gp_ccname'][$key] as $ckey => $cval){
					if(empty($cval)) continue;
					
					if($_var['gp_cmenuid'][$key][$ckey]) {
						$_wx_menu->update($_var['gp_cmenuid'][$key][$ckey], array(
						'PARENTID' => $menuid,
						'NAME' => $cval,
						'URL' => $_var['gp_curl'][$key][$ckey],
						'REMARK' => $_var['gp_cremark'][$key][$ckey],
						'DISPLAYORDER' => $_var['gp_cdisplayorder'][$key][$ckey] + 0,
						'USERID' => $_var['current']['USERID'],
						'USERNAME' => $_var['current']['USERNAME'],
						'EDITTIME' => date('Y-m-d H:i:s')
						));
					}else{
						$_wx_menu->insert(array(
						'PARENTID' => $menuid, 
						'NAME' => $cval,
						'URL' => $_var['gp_curl'][$key][$ckey],
						'REMARK' => $_var['gp_cremark'][$key][$ckey],
						'DISPLAYORDER' => $_var['gp_cdisplayorder'][$key][$ckey] + 0,
						'USERID' => $_var['current']['USERID'],
						'USERNAME' => $_var['current']['USERNAME'],
						'EDITTIME' => date('Y-m-d H:i:s')
						));
					}
				}
				
				unset($menuid);
			}
		}
		
		if(is_array($_var['gp_cbxItem']) && $_var['gp_do'] == 'delete_list'){
			$menu_names = '';
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$menu = $_wx_menu->get_by_id($val);
				if($menu){
					$_wx_menu->delete($menu['WX_MENUID']);
					
					$menu_names .= "{$menu[NAME]}，";
				}
				
				unset($menu);
			}
			
			if($menu_names) $_log->insert($GLOBALS['lang']['wx.menu.log.delete.list']."({$menu_names})", $GLOBALS['lang']['wx.menu']);
		}
		
		$weixin_menus = array();
		
		$wx_menus = $_wx_menu->get_tree();
		foreach($wx_menus as $key => $wx_menu){
			$temparr = explode(',', $wx_menu['URL']);
			$weixin_menu = array();
			
			if(count($wx_menu['CHILDREN']) == 0){
				if(is_cint($temparr[0]) || is_ansi($temparr[0])){
					$weixin_menu['type'] = 'click';
					$weixin_menu['name'] = urlencode($wx_menu['NAME']);
					$weixin_menu['key'] = $temparr[0];
				}else{
					$weixin_menu['type'] = 'view';
					$weixin_menu['name'] = urlencode($wx_menu['NAME']);
					$weixin_menu['url'] = substr($wx_menu['URL'], 0, 7) == 'http://' || substr($wx_menu['URL'], 0, 8) == 'https://' ? $wx_menu['URL'] : $setting['SiteHost'].$wx_menu['URL'];
				}
			}else{
				$weixin_menu['name'] = urlencode($wx_menu['NAME']);
				$weixin_menu['sub_button'] = array();
				
				foreach($wx_menu['CHILDREN'] as $ckey => $cwx_menu){
					$ctemparr = explode(',', $cwx_menu['URL']);
					$cweixin_menu = array();
					
					if(is_cint($ctemparr[0]) || is_ansi($ctemparr[0])){
						$cweixin_menu['type'] = 'click';
						$cweixin_menu['name'] = urlencode($cwx_menu['NAME']);
						$cweixin_menu['key'] = $ctemparr[0];
					}else{
						$cweixin_menu['type'] = 'view';
						$cweixin_menu['name'] = urlencode($cwx_menu['NAME']);
						$cweixin_menu['url'] = substr($cwx_menu['URL'], 0, 7) == 'http://' || substr($cwx_menu['URL'], 0, 8) == 'https://' ? $cwx_menu['URL'] : $setting['SiteHost'].$cwx_menu['URL'];
					}
					
					$weixin_menu['sub_button'][] = $cweixin_menu;
					unset($ctemparr);
				}
			}
			
			$weixin_menus[] = $weixin_menu;
			
			unset($temparr);
		}
		
		$http_text = urldecode(json_encode(array('button' => $weixin_menus)));
		
		if($_var['gp_formsubmit'] && count($wx_menus) > 0){
			$access_token = $_wx->token($wx_setting['WX_APPID'], $wx_setting['WX_SECRET']);
			$result = $_wx->request("https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}", $http_text, 'POST');
			$result = json_decode($result, 1);
		}
		
		include_once view('/module/wx/view/menu');
	}
	
}
?>