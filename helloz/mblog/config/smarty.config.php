<?php
	$path = dirname(dirname(__FILE__));
	include($path.'/smarty/Smarty.class.php');
	
	$smarty = new Smarty;
	
	$smarty->caching = false;
	$smarty->template_dir = $path.'/templates';
	$smarty->compile_dir = $path.'/templates_c';		//设置编译目录
	$smarty->cache_dir = $path.'/cache';					//设置缓存目录 
	$smarty->left_delimiter = "{";
	$smarty->right_delimiter = "}"; 
?>