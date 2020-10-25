<?php
//写入文件数据
function save_config($param){
	// 检测目录是否存在
	$dirname = APP_PATH.'Radio/';
	if(!is_dir($dirname)){
		return $dirname.'目录不存在';
	}
	// 整理内容
	$config_content = "<?php\nreturn ".var_export($param['config'], true).";";
	$config_content = str_replace('	', "\t", $config_content);
	if(!file_put_contents($dirname.$param['file'], $config_content)){
		return '写入'.$config_file.'失败';
	}
	// 保存成功
	return true;
}