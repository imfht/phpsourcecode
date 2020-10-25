<?php
require '../includes/init.php';
//删除编译文件
$cdir = opendir(COMP_DIR);
while (false !== $file = readdir($cdir)){
	if ($file == '.' || $file =='..') {
		continue;
	}
	unlink(COMP_DIR.$file);
}
closedir($cdir);

//删除缓存文件
$tdir = opendir(CACHE_DIR);
while (false !== $file = readdir($tdir)){
	if ($file == '.' || $file =='..') {
		continue;
	}
	unlink(CACHE_DIR.$file);
}
closedir($tdir);
return '<script type="text/javascript">alert("缓存清除成功！");</script>';
?>