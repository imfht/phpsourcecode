<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

/**
 * 模块
 * @author sigmazel
 * @since v1.0.2
 */
class _module{
	//获取所有模块
	public function get_all(){
		$modules = array();
		$dirs = scandir(ROOTPATH.'/module/');
		
		foreach ($dirs as $file){
			if(is_dir(ROOTPATH.'/module/'.$file) && $file != '.' && $file != '..' && is_file(ROOTPATH."/module/{$file}/_info.xml")){
				if(is_dir(ROOTPATH."/module/{$file}/update")){
					$udirs = scandir(ROOTPATH."/module/{$file}/update");
					$update = false;
					foreach ($udirs as $ufile){
						if(is_file(ROOTPATH."/module/{$file}/update/{$ufile}")) $update = true;
					}
				}
				
				$xml = (array)simplexml_load_file(ROOTPATH.'/module/'.$file.'/_info.xml');
				$xml['update'] = $update;
				
				if(is_array($xml)){
					$xml['state'] = is_file(ROOTPATH."/_cache/module/{$xml[id]}/install.lock") ? 'installed' : '';
					$modules[$xml['id']] = $xml;
				}
			}
			
			unset($udirs);
			unset($update);
			unset($xml);
		}
		
		return $modules;
	}
	
	//获取已安装模块
	public function get_installed($user = null){
		global $db, $ADMIN_SCRIPT;
		
		if(!$user) return array();
		
		$modules = array();
		
		if($user['USERID'] == -1) $temp_query = $db->query("SELECT * FROM tbl_menu WHERE PARENTID = 0 AND TYPE = 2 ORDER BY DISPLAYORDER ASC");
		else $temp_query = $db->query("SELECT m.* FROM tbl_menu m ,tbl_role_menu rm WHERE m.MENUID = rm.MENUID AND rm.ROLEID = '{$user[ROLEID]}' AND m.PARENTID = 0 AND m.TYPE = 2 ORDER BY DISPLAYORDER ASC");
		
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['URL'] = str_replace('{$ADMIN_SCRIPT}', $ADMIN_SCRIPT, $row['URL']);
			$modules[$row['MENUID']] = $row;
		}
		
		return $modules;
	}
	
}
?>