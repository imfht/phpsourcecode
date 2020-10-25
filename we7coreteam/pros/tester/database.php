<?php
use Testify\Testify;

require '../framework/bootstrap.inc.php';
require IA_ROOT . '/framework/library/testify/Testify.php';
//防止30秒运行超时的错误（Maximum execution time of 30 seconds exceeded).
set_time_limit(0);
load()->func('communication');

$tester = new Testify('测试数据库');

$tester->test('数据库备份', function() {
	global $tester;
	//登录
	$loginurl = 'http://pros.we7.cc/web/index.php?c=user&a=login&';
	$response = ihttp_get($loginurl);
	preg_match('/name="token" value="(.*?)" type="hidden"/i', $response['content'], $matchs);
	$token = $matchs['1'];
	$username = 'admin';
	$password = '123456';
	$submit = '登录';
	$post = array(
		'username' => $username,
		'password' => $password,
		'submit' => $submit,
		'token' => $token
	);
	$responses = ihttp_request($loginurl, $post);
	$login_success = '欢迎回来，admin。';
	$result = strpos($responses['content'],$login_success);
	if (empty($result)) {
		$sign = false;
	} else {
		$sign = true;
	}
	$cookiejar =$responses['headers']['Set-Cookie'];
	$tester->assertEquals($sign, true);
	//备份
	$backupurl = 'http://pros.we7.cc/web/index.php?c=system&a=database&do=backup&status=1&start=2';
	$response1 = ihttp_request($backupurl,'',array(
		'CURLOPT_COOKIE' => implode(';' , $cookiejar)));
	preg_match('/href="\.\/index.php\?c=system&a=database&do=backup&last_table=(.*?)&index=(.*?)&series=(.*?)&volume_suffix=(.*?)&folder_suffix=(.*?)&status=1"/i', $response1['content'], $match);
	$series = $match['3'] -1;
	$volume_suffix = "volume-".$match['4']."-".$series.".sql";
	$folder_suffix = $match['5'];
	$dir = IA_ROOT . '/data/backup';
	function rmdi_r($dirname) {
		$data = array();
		//判断是否为一个目录，非目录直接关闭
		if (is_dir($dirname)) {
			//如果是目录，打开他
			$name=opendir($dirname);
			//使用while循环遍历
			while (false !== ($file = readdir($name))) {
				//去掉本目录和上级目录的点
				if ($file=="." || $file=="..") {
					continue;
				}
				//如果目录里面还有一个目录，再次回调
				if (is_dir($dirname."/".$file)) {
					$result = rmdi_r($dirname."/".$file);
					$data = array(
						'dirname'=> $result['dirname'],
						'file'=> $result['file']
					);
				}
				if (is_file($dirname."/".$file)) {
					$data = array(
						'dirname'=> $dirname,
						'file'=> $file
					);
				}
			}
		//遍历完毕关闭文件
		closedir($name);
		return $data;
		}
	}
	$data = rmdi_r($dir);
	$start = strripos($data['dirname'],'/')+1;
	$dirname = substr($data['dirname'], $start);
	if ($dirname==$folder_suffix) {
		$backup_dir_sign = true;
	} else {
		$backup_dir_sign = false;
	}
	if ($data['file']==$volume_suffix) {
		$backup_file_sign = true;
	} else {
		$backup_file_sign = false;
	}
	$backup_success = "正在导出数据, 请不要关闭浏览器, 当前第 1 卷.";
	$result1 = strpos($response1['content'],$backup_success);
	if (empty($result1)) {
		$backup_sign = false;
	} else {
		$backup_sign = true;
	}
	$tester->assertEquals($backup_dir_sign, true);
	$tester->assertEquals($backup_file_sign, true);
	$tester->assertEquals($backup_sign, true);
	
});

$tester->test('数据库还原', function() {
	global $tester ,$restore_volume_prefix ,$series , $dirname , $dir;
	//登录
	$loginurl = 'http://pros.we7.cc/web/index.php?c=user&a=login&';
	$response = ihttp_get($loginurl);
	preg_match('/name="token" value="(.*?)" type="hidden"/i', $response['content'], $matchs);
	$token = $matchs['1'];
	$username = 'admin';
	$password = '123456';
	$submit = '登录';
	$post = array(
		'username' => $username,
		'password' => $password,
		'submit' => $submit,
		'token' => $token
	);
	$responses = ihttp_request($loginurl, $post);
	$login_success = '欢迎回来，admin。';
	$result = strpos($responses['content'],$login_success);
	if (empty($result)) {
		$sign = false;
	} else {
		$sign = true;
	}
	$cookiejar =$responses['headers']['Set-Cookie'];
	$tester->assertEquals($sign, true);
	//还原前抽样获取数据库某张表中有多少条记录
	$start_old_sql = 'SELECT COUNT(*) FROM' .tablename(account);
	$start_old_record_num = pdo_fetchcolumn($start_old_sql);
	
	$middle_old_sql = 'SELECT COUNT(*) FROM' .tablename(mc_mapping_fans1);
	$middle_old_record_num = pdo_fetchcolumn($middle_old_sql);
	
	$end_old_sql = 'SELECT COUNT(*) FROM' .tablename(wechat_attachment);
	$end_old_record_num = pdo_fetchcolumn($end_old_sql);
	//还原备份的目录
	$dir = IA_ROOT . '/data/backup/';
	$dirname = "1478487306_e36334Nr";
	$restore_dirname = $dir . $dirname;
	//统计备份文件的数量
	$num = count(scandir($restore_dirname));
	//去掉本目录和上级目录的点
	$restore_file_num = $num -2;
	//开始还原备份
	for ( $i=1; $i<=$restore_file_num; $i++) {
		if ($i==1) {
			$restoreurl = "http://pros.we7.cc/web/index.php?c=system&a=database&do=restore&restore_dirname=" .$dirname;
		} else {
			$restoreurl = "http://pros.we7.cc/web/index.php?c=system&a=database&do=restore&restore_dirname=" .$dirname ."&restore_volume_prefix=" . $restore_volume_prefix . "&restore_volume_sizes=" . $series;
		}
		$response1 = ihttp_request($restoreurl,'',array(
			'CURLOPT_COOKIE' => implode(';' , $cookiejar)));
		preg_match('/href="\.\/index.php\?c=system&a=database&do=restore&restore_dirname=(.*?)&restore_volume_prefix=(.*?)&restore_volume_sizes=(.*?)"/i', $response1['content'], $match);
	 	$restore_volume_prefix = $match[2];
		$series = $match[3]-1;
	 	$restore_file = $dir . $match[1] . "/volume-" . $restore_volume_prefix . "-" . $series . ".sql";
		//备份目录下是否有该备份文件
		if (is_file($restore_file)) {
			$restore_sign . "_" . $series = true;
		} else {
			$restore_sign . "_" . $series = false;
		}
		$tester->assertEquals($restore_sign . "_" . $series, true);
		//是否提醒成功恢复该备份文件
		$restore_success = "正在恢复数据备份, 请不要关闭浏览器, 当前第" . $series ."卷";
		$result1 = strpos($response1['content'],$restore_success);
		if (empty($result1)) {
			$restore_success_sign . "_" . $series = false;
		} else {
			$restore_success_sign . "_" . $series = true;
		}
		$tester->assertEquals($restore_success_sign . "_" . $series, true);
	}
	//某张表在还原后与还原前对比
	$start_new_sql = 'SELECT COUNT(*) FROM' .tablename(account);
	$start_new_record_num = pdo_fetchcolumn($start_new_sql);
	$tester->assertEquals($start_old_record_num, $start_new_record_num);
	
	$middle_new_sql = 'SELECT COUNT(*) FROM' .tablename(mc_mapping_fans1);
	$middle_new_record_num = pdo_fetchcolumn($middle_new_sql);
	$tester->assertEquals($middle_old_record_num, $middle_new_record_num);
	
	$end_new_sql = 'SELECT COUNT(*) FROM' .tablename(wechat_attachment);
	$end_new_record_num = pdo_fetchcolumn($end_new_sql);
	$tester->assertEquals($end_old_record_num, $end_new_record_num);
});
$tester->run();