<?php
if(!defined('DIR')){
	exit('Please correct access URL.');
}

require_once DIR_CLASS.'smarty/Smarty.class.php';
$smarty = new Smarty();
$smarty->template_dir = DIR_TPL;
$smarty->compile_dir = DIR.'tpl_c/';
