<?php
set_time_limit ( 0 );

$system=new \Think\Db();
$config=new \Think\Config();



$site_config = $system::name('system')->where('name','version')->value('value');




if(empty($site_config)){
	$data['name']='version';
	$data['value']=1;
	$system::name('system')->insert($data);
	$site_config =1;
}

 if ($site_config < 7) {

	return json(array('code' => 0, 'msg' => '该补丁包需要先升级到1.0.7版本再升级'));

}
if ($site_config == 8) {
	return json(array('code' => 0, 'msg' => '您已经是最新版本'));

} 



$dirname=dirname(__FILE__);



$install_sql = $dirname. '/update.sql';



if(file_exists($install_sql)){
	$db_config = array();
	
	$db_config['prefix'] = $config::get('database.prefix');
	
	
	$sqldata =file_get_contents($install_sql);
	$sql_array=preg_split("/;[\r\n]+/", str_replace('ea_',$db_config['prefix'],$sqldata));
	foreach ($sql_array as $k => $v) {
		if (!empty($v)) {
			$system::query($v);
		}
	}
}


        $system::name('system')->where('name', 'version')->setField('value',8);

return json(array('code' => 200, 'msg' => '更新完毕，请清理缓存并重新登录'));
