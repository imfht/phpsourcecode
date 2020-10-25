<?php
define('ECROOT',dirname(__FILE__));
define('EasyChart',true);

//加载基本配置
$EC_config=array();
if (file_exists(ECROOT.'/config.php')) $EC_config=require(ECROOT.'/config.php');

function EasyChartLoader($class_name) {
	if (file_exists(ECROOT.'/class/'.$class_name.'.php')){
		require ECROOT.'/class/'.$class_name.'.php';
	}
}
spl_autoload_register('EasyChartLoader');

?>
