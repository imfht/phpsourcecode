<?php
set_time_limit ( 0 );
$prefix = C ( 'DB_PREFIX' );

$map ['name'] = 'SYSTEM_UPDATRE_VERSION';
$res = M ( 'config' )->where ( $map )->getField ( 'value' );

if ($res < 20150127) {
	$this->mtReturn(300, '该补丁包需要先升级到20150127发布补丁包版本再升级' );
	
	exit ();
}
if ($res >= 20150218) {
	$this->mtReturn(300, '请不要重复执行数据库升级'  );
	
	exit ();
}

unset ( $map );

$install_sql = './update/update.sql';
execute_sql_file ( $install_sql );

$map ['name'] = 'NICK_NAME_BAOLIU';
$data=array('title'=>'保留昵称','remark'=>'禁止注册昵称,用半角逗号隔开','value'=>'管理员,测试,admin,垃圾');
M ( 'config' )->where ( $map )->setField ($data);

$map ['name'] = 'VERIFY_OPEN';
$data1=array('title'=>'验证码配置','remark'=>'验证码配置，填写数字，数字中间用半角逗号隔开。1:注册显示2:登陆显示3:后台登陆显示');
M ( 'config' )->where ( $map )->setField ($data1);


$map ['name'] = 'CATE_TYPE';
M ( 'config' )->where ( $map )->setField ( 'value', '1:博客');


$map ['name'] = 'SYSTEM_UPDATRE_VERSION';
$res = M ( 'config' )->where ( $map )->setField ( 'value', 20150218 );
S ( 'DB_CONFIG_DATA', null );

$this->mtReturn(200,'更新完毕，请清理缓存！','','forward',U('Update/index'));