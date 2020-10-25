<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */
 
session_start();
require_once (dirname(__FILE__) . "/config.inc.php");
$conn_cfg = require_once (CONF."/conn.php");
require(PJINC.'/core/model.lib.php');
$nf_m = new Model($conn_cfg);
include(PJINC.'/core/filesys.lib.php');
include (PJINC.'/cfg_write.inc.php');
$nf_d = new filesys;
$nf_d->charset = 'UTF-8';
set_time_limit(1000);
if(file_exists(RUNTIME.'/data.php')){
	$info = file_get_contents(RUNTIME.'/data.php');
	$info = json_decode($info,true);
	if($info['data']['adminuser']){
		require_once(RUNTIME.'/data_copy.php');
		$info = $info_copy;
	}
}else{
	echo '<script>setTimeout("window.location=\'error.php?show=无法读取数据\'",0);</script>';
	exit();
}

$act = $_POST['act'];
$go = $_POST['go'];
if($act=='redb'){
	if($go==count($info['file'])){
		$fields = array(
			'username'=>$info['data']['adminuser'],
			'realname'=>$info['data']['adminuser'],
			'password'=>md5($info['data']['adminpwd']),
			'email'=>$info['data']['mail'],
			'MailPwd'=>'',
			'access'=>9999,
			'date_created'=>time(),
		);
		$add = $nf_m->add($nf_m->db_prefix.'user_table',$fields);
		unset($fields);
		
		$fields = array(
			'user_id'=>1,
			'group_id'=>1,
		);
		$add = $nf_m->add($nf_m->db_prefix.'user_main_table',$fields);
		unset($fields);
		
		$fields = array(
			'vals' => $info['data']['webname'],
			'opts' => $info['data']['webname'],
		);
		$add = $nf_m->up($nf_m->db_prefix.'config',$fields,'CFG_NAME','keyword');
		unset($fields);
		
		$fields = array(
			'vals' => aCslas($info['data']['hostname']),
			'opts' => aCslas($info['data']['hostname']),
		);
		$add = $nf_m->up($nf_m->db_prefix.'config',$fields,'CFG_HOST','keyword');
		
		cfg_write($nf_m->sele($nf_m->db_prefix.'config',NULL,'id asc, '));
		if(!file_exists(PJROOT.'/look.txt')){
			file_put_contents(PJROOT.'/lock.txt','ok');
		}
		@unlink(RUNTIME.'/data.php');
		@unlink(RUNTIME.'/data_copy.php');
		echo '所有表已完成创建！'; exit;
	}
	if($info['file'][$go]){
		$table = str_replace('#@_',$conn_cfg['DB_PREFIX'],$info['file'][$go]);
		$tb = str_replace('.bak','',$table);
		$tablefile = PJDATA.'/'.$info['file'][$go];
		$data = $nf_d->getFile($tablefile);
		$arr_data = explode(";\r\n",$data);
		foreach($arr_data as $t){
			$t = preg_replace("/`#@_(.+)?`/iu",'`'.$conn_cfg['DB_PREFIX'].'$1`',$t);
			$t = preg_replace("/ENGINE=\b.{2,}\b DEFAULT CHARSET=\S+/",'ENGINE=MyISAM DEFAULT CHARSET=utf8',$t);
			$nf_m->add($t,NULL);
		}
		echo '表"'.$tb.'"创建成功！'."\r\n";
	}
	exit;
}elseif($act=='put'){
	$serial = strval($_POST['serial']);
	$fields = array(
		'vals' => $serial,
		'opts' => $serial,
	);
	$add = $nf_m->up($nf_m->db_prefix.'config',$fields,'CFG_APPID','keyword');
	unset($fields);
}


//获取数据表
function get_table(){
	global $nf_m;
	$info = $nf_m->sele('show tables from '.$nf_m->db_name,NULL,NULL,NULL,2,2);
	$infos = array();
	foreach($info as $a){
		$infos[] = $a[0];
	}
	return $infos;
	unset($info,$infos);
}