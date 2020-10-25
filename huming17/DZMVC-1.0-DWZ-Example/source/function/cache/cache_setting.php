<?php

/**
 * 基本缓存设置
 * @author HumingXu E-mail:huming17@126.com
 */
function build_cache_setting() {
    global $_G;

    $skipkeys = array('backupdir', 'custombackup');
    $serialized = array('seccodedata', 'secqaa', 'server_mail', 'server_upload');

    $data = array();

    foreach (C::t('common_setting')->fetch_all_not_key($skipkeys) as $setting) {
        if (in_array($setting['skey'], $serialized)) {
            $_G['setting'][$setting['skey']] = $data[$setting['skey']] = json_decode($setting['svalue'], 1);
        } else {
            $_G['setting'][$setting['skey']] = $data[$setting['skey']] = $setting['svalue'];
        }
    }
    list($data['user_role_menu']) = get_cachedata_user_role_menu();
	//TODO 数据文件缓存
	//$data['footernavs'] = get_cachedata_footernav();
	//$data['spacenavs'] = get_cachedata_spacenavs();
	//$data['mynavs'] = get_cachedata_mynavs();
	//$data['topnavs'] = get_cachedata_topnav();
	//	unset($data['allowthreadplugin']);
	//	if($data['jspath'] == 'data/cache/') {
	//		writetojscache();
	//	} elseif(!$data['jspath']) {
	//		$data['jspath'] = 'static/js/';
	//	}
	//
	//	if($data['cacheindexlife']) {
	//		$cachedir = SITE_ROOT.'./cache/';
	//		$tidmd5 = substr(md5(0), 3);
	//		@unlink($cachedir.'/'.$tidmd5[0].'/'.$tidmd5[1].'/'.$tidmd5[2].'/0.htm');
	//	}
    savecache('setting', $data);
    $_G['setting'] = $data;
}

/*
* 获取用户角色菜单表数据 缓存至 common_syscache
* @return array user_role_menu
*/
function get_cachedata_user_role_menu(){
    global $_G;
	//DEBUG 取出所有角色菜单
	$data['user_role_menu'] = array(
		'role_menu'=>array(
			//'role_id' => array(	
				//'menu_url_md5'=>array(),
				//'menu_url_tree'=>array()
			//)
		),
		'user_menu'=>array(
			//'user_id' => array(
				//'menu_url_md5'=>array(),
				//'menu_url_tree'=>array()
			//)
		)
	);
    $user_role_menu_sql_results = array();
    $user_role_menu_sql = "SELECT cm.menu_id,cm.menu_pid,cm.position,cm.sub_position,cm.name_var,cm.url,cm.self_style,cm.sort,urm.role_id,urm.user_id 
    FROM " . DB::table('user_role_menu') . " AS urm 
    LEFT JOIN " . DB::table('common_menu') . " AS cm ON urm.menu_id = cm.menu_id 
    WHERE cm.enable = 1 AND cm.isdelete = 0 
    ORDER BY cm.menu_pid ASC, cm.sort ASC";
    $user_role_menu_sql_results = DB::fetch_all($user_role_menu_sql);
    //按角色 和 用户 格式化菜单数据 KEY 为 $key !empty(sub_position) = md5(url.'&'.sub_position) else md5(url);
    foreach ($user_role_menu_sql_results as $tkey => $tvalue) {
  		if($tvalue['url'] || $tvalue['sub_position']){
  			$tmp_key= empty($tvalue['sub_position']) ? md5($tvalue['url']) : md5($tvalue['url'].'&'.$tvalue['sub_position']);
  		}else{
  			$tmp_key= md5($tvalue['menu_id']);
  		}
  		if($tvalue['role_id']){
  			$data['user_role_menu']['role_menu'][$tvalue['role_id']]['menu_url_md5'][$tmp_key] = 1;
  			$data['user_role_menu']['role_menu'][$tvalue['role_id']]['menu_url_tree'][$tvalue['menu_id']] = $tvalue;
  		}
  		if($tvalue['user_id']){
  			$data['user_role_menu']['user_menu'][$tvalue['user_id']]['menu_url_md5'][$tmp_key] = 1;
  			$data['user_role_menu']['user_menu'][$tvalue['user_id']]['menu_url_tree'][$tvalue['menu_id']] = $tvalue;
  		}
    }
    //DEBUG 格式化权限菜单数
    foreach($data['user_role_menu']['role_menu'] AS $key => $value){
    	$data['user_role_menu']['role_menu'][$key]['menu_url_tree'] = menuarr2tree($value['menu_url_tree']);
    }
    foreach($data['user_role_menu']['user_menu'] AS $key => $value){
    	$data['user_role_menu']['user_menu'][$key]['menu_url_tree'] = menuarr2tree($value['menu_url_tree']);
    }
    return array($data['user_role_menu']);
}
?>