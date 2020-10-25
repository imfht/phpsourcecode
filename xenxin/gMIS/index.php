<?php

$_REQUEST['tbl'] = ''; #  'fin_todotbl'; Wed Oct 22 09:10:01 CST 2014

require("./comm/header.inc.php");
$data['title'] = $data['lang']['agentname'];
$out = str_replace('TITLE', $data['title'], $out);

$gtbl = new WebApp();

$module_list = ""; $hm_module_order = array();  $hm_module_name = array(); $hm_todo_list = array();
$hm_module_db = array();
$moduleNeedDb = '';

$userGroup = $user->getGroup();
$hm = $gtbl->execBy($sql="select * from ".$_CONFIG['tblpre']."fin_todotbl where 1=1 and ((togroup in (" # for multiple groups
        .$userGroup.") or touser=".$user->getId()." or triggerbyparent in (".$userGroup.") or triggerbyparentid="
        .$user->getId().") or $userGroup=1) and istate in (1,2) order by istate desc, id desc limit 7 ", null,
		$withCache=array('key'=>'info_todo-select-'.$user->getId()));
		# give overall data to admin grouplevel=1, Nov 10, 2018
#debug("sql:$sql");
if($hm[0]){
    $hm = $hm[1];
    foreach ($hm as $k=>$v){
        $hm_todo_list[$v['id']] = $v;
    }
}
$data['todo_state'] = array('0'=>$lang->get('work_task_state_done'), 
	'1'=>$lang->get('work_task_state_todo'), 
	'2'=>$lang->get('work_task_state_doing'), 
	'3'=>$lang->get('work_task_state_pending'), 
	'4'=>$lang->get('work_task_state_cancel'));
$data['user_list'] = $user->getUserList();

$mycachedate=date("Y-m-d", time()-(86400*60));
$hm = $gtbl->execBy("select count(parenttype) as modulecount, parenttype from "
        .$_CONFIG['tblpre']."fin_operatelogtbl where inserttime > '"
        .$mycachedate." 00:00:00' and parenttype not in ('gmis_info_usertbl', 'gmis_fin_todotbl')"
        ." group by parenttype order by modulecount desc limit 11", null,
	$withCache=array('key'=>'fin_operatelog-select-'.$mycachedate));
if($hm[0]){
	$hm = $hm[1];
	if(is_array($hm)){
	foreach($hm as $k=>$v){
		$module_list .= "'".$v['parenttype']."',";
		$hm_module_order[$k] = $v['parenttype'];
	}
	}
	$module_list = substr($module_list, 0, strlen($module_list)-1);
	$hm = $gtbl->execBY("select objname,tblname from "
	        .$_CONFIG['tblpre']."info_objecttbl where tblname in ($module_list)", null,
		$withCache=array('key'=>'info_object-select-'.$module_list));
	if($hm[0]){
		$hm = $hm[1];
		if(is_array($hm)){
		foreach($hm as $k=>$v){
			$hm_module_name[$v['tblname']] = $v['objname'];
		}
		}
	}
	$moduleNeedDb = $module_list;
}

#
$hm = $gtbl->execBy("select objname,tblname from "
        .$_CONFIG['tblpre']."info_objecttbl where addtodesktop > 0 order by addtodesktop", null,
	$withCache=array('key'=>'info_object-select-desktop'));
if($hm[0]){
	$hm = $hm[1];
	$data['module_list_byuser'] = $hm; #Todo add2desktop by user
}
else{
    $hm = $gtbl->execBy("select objname,tblname from ".$_CONFIG['tblpre']."info_objecttbl order by rand() limit 11",
            null, $withCache=array('key'=>'info_object-select-desktop-rand'));
    if($hm[0]){
        $hm = $hm[1];
        $data['module_list_byuser'] = $hm;
    }
	else{
        $data['module_list_byuser'] = array();
	}
}

#
$module_list = '';
foreach($data['module_list_byuser'] as $k=>$v){
    $module_list .= "'".$v['parenttype']."',";
    $module_list = substr($module_list, 0, strlen($module_list)-1);
}
if($moduleNeedDb == ''){
	$moduleNeedDb = '\'\'';	
}
if($module_list != ''){
	$moduleNeedDb .= ','.$module_list;
}
$hm = $gtbl->execBY("select modulename,thedb from ".$_CONFIG['tblpre']
        ."info_menulist where modulename in ($moduleNeedDb)", null,
        $withCache=array('key'=>'info_menulist-select-'.$module_list));
if($hm[0]){
    $hm = $hm[1];
    foreach($hm as $k=>$v){
        $hm_module_db[$v['modulename']] = $v['thedb'];
    }
}

#
$hm = $gtbl->execBy("select count(*) as modulecount from ".$_CONFIG['tblpre']."info_objecttbl where istate=1", null,
	$withCache=array('key'=>'info_object-select-count'));
if($hm[0]){
	$hm = $hm[1];
	$data['module_count'] = $hm[0]['modulecount'];
}

$userListOL = array();
$hm = $gtbl->execBy("select id, email from "
	.$_CONFIG['tblpre']."info_usertbl where istate=1", null, 
	$withCache=array('key'=>'info_user-select-count'));
if($hm[0]){
	$hm = $hm[1];
	$data['user_count'] = count($hm);
	foreach($hm as $k=>$v){
        $userListOL[$v['id']] = $v['email'];
    }
}

$hm = $gtbl->execBy("select * from ".$_CONFIG['tblpre']."fin_operatelogtbl order by ".$gtbl->getMyId()." desc limit 7",
        null, $withCache=array('key'=>'info_user-select-log'));
if($hm[0]){
	$hm = $hm[1];
	$data['log_list'] = $hm;
}

# dir list, added by wadelau@ufqi.com, Sat Mar 12 12:45:24 CST 2016
$navidir = $_REQUEST['navidir'];
if($navidir != ''){
	$hm = $gtbl->execBy("select * from ".$_CONFIG['tblpre']."info_menulist where levelcode='".$navidir
	        ."' or levelcode like '".$navidir."__'  order by levelcode", null,
	        $withCache=array('key'=>'info_menulist-select-by-level-'.$navidir));
	if($hm[0]){
		$hm = $hm[1];
		$data['navidir_list'] = $hm;
	}
	else{
		$data['navidir_list'] = array();
	}
	#debug($hm, '', 1);
}

$fp = fopen("./ido.php", "r");
if($fp){
	$fstat = fstat($fp);
	fclose($fp);
	$mtime = $fstat['mtime'];
	$data['system_lastmodify'] = date("Y-m-d", $mtime);
}
$data['start_date'] = $_CONFIG['start_date'];

# today's users count
$logged_user_count = 1;
$mycachedate=date("Y-m-d", time()-(86400*1));
$hm = $gtbl->execBy("select userid from "
        .$_CONFIG['tblpre']."fin_operatelogtbl where inserttime >= '"
        .$mycachedate." 00:00:00'" #
        ." group by userid", null,
        $withCache=array('key'=>'fin_operatelog-select-usercount-'.$user->getId().'-'.$mycachedate));
if($hm[0]){
    #debug($hm);
    $hm = $hm[1];
    $logged_user_count = count($hm);
}

# module path
$module_path = ''; $levelcode = ''; $codelist = '';
include_once($appdir."/comm/modulepath.inc.php");

if(true){
    $out .= "<script type=\"text/javascript\">currenttbl='".$tbl."';\ncurrentdb='"
        .$mydb."';\n currentlistid= {};\n currentpath='".$rtvdir."';\n userinfo={"
        ."'id':'".$userid
        ."','email':'".$user->getEmail()
        ."','group':'".$user->getGroup()
        ."','branch':'".$user->get('branchoffice')
        ."','sid':'".$sid
        ."'};\n </script>\n";
}

$data['logged_user_count'] = $logged_user_count;
$data['module_list_order'] = $hm_module_order;
$data['module_list_name'] = $hm_module_name;
$data['module_list_db'] = $hm_module_db;
$data['todo_list'] = $hm_todo_list;
$data['module_path'] = $module_path;
$data['user_list_ol'] = $userListOL;

$data['lang']['welcome_back'] = $lang->get('welcome_back');
$data['lang']['navi_homepage'] = $lang->get('navi_homepage');
$data['lang']['navi_dir'] = $lang->get('navi_dir');
$data['lang']['todayis'] = $lang->get('todayis');
$data['lang']['work_todo'] = $lang->get('menu_desktop_todo');
$data['lang']['work_task'] = $lang->get('work_task');
$data['lang']['state'] = $lang->get('state');
$data['lang']['demand'] = $lang->get('demand');
$data['lang']['supply'] = $lang->get('supply');
$data['lang']['updatetime'] = $lang->get('updatetime');
$data['lang']['more'] = $lang->get('more');
$data['lang']['user'] = $lang->get('user');
$data['lang']['object'] = $lang->get('object');
$data['lang']['module'] = $lang->get('module');
$data['lang']['sys_online'] = $lang->get('sys_online');
$data['lang']['online_user'] = $lang->get('online_user');
$data['lang']['homesite'] = $lang->get('open_homesite');

$data['lang']['operation'] = $lang->get('operation');
$data['lang']['mostused'] = $lang->get('navi_mostused');
$data['lang']['mostused_hint'] = $lang->get('navi_mostused_hint');
$data['lang']['desktop_shortcut'] = $lang->get('navi_desktop');
$data['lang']['desktop_shortcut_hint'] = $lang->get('navi_desktop_hint');
$data['lang']['operatelog'] = $lang->get('navi_operatelog');
$data['lang']['operatelog_hint'] = $lang->get('navi_operatelog_hint');

$smttpl = getSmtTpl(__FILE__, $act);

$smt->assign('agentname', $_CONFIG['agentname']);
$smt->assign('welcomemsg',$welcomemsg);
$smt->assign('desktopurl', $url);
$smt->assign('url', $url);
$smt->assign('ido', $ido);
$smt->assign('jdo', $jdo);
$smt->assign('today', date("Y-m-d"));
$smt->assign('historyurl', $ido.'&tbl=info_operatelogtbl&tit='.$lang->get('menu_desktop_operatelog').'&a1=0&pnsktogroup='
	.$userGroup.'&pnskuserid='.$userid);

$navi = new PageNavi();

$pnsc = "state=? and (touser like '".$user->getId()."' or togroup like '".$userGroup."')";
$smt->assign('todourl','ido.php?tbl=fin_todotbl&tit='.$lang->get('menu_desktop_todo').'&a1=1&pnskistate=0&pnsm=1&pnsktouser='.$userid
	.'&pnsc='.$pnsc.'&pnsck='.$navi->signPara($pnsc).'&pnsktogroup='.$userGroup);

$smt->assign('sid', $sid);
$smt->assign('userid', $userid);
$smt->assign('content',$out);
$smt->assign('rtvdir', $rtvdir);
$smt->assign('isheader', $isheader);
$smt->assign('watch_interval', $_CONFIG['watch_interval']);

$watchRld = Wht::get($_REQUEST, 'watchRld');
$watchRld = $watchRld=='' ? 1 : $watchRld;
$smt->assign('watch_interval_reload', $watchRld);

require("./comm/footer.inc.php");

?>