<?php

define('IN_SITE', TRUE);
define('IN_ADMINCP', TRUE);

chdir('../../');

require_once './source/class/class_core.php';

$dzf = C::app();
$dzf->init_cron = false;
$dzf->init_session = false;
$dzf->init();

if($_GET['key'] !== md5($_G['setting']['authkey'].$_SERVER['REMOTE_ADDR'])) {
	exit;
}

$plugins = array('qqconnect', 'cloudstat', 'soso_smilies', 'cloudsearch', 'qqgroup', 'security', 'xf_storage');

require_once libfile('function/plugin');
require_once libfile('function/admincp');
require_once libfile('function/cache');

foreach($plugins as $pluginid) {
	$importfile = DZF_ROOT.'./source/plugin/'.$pluginid.'/core_plugin_'.$pluginid.'.xml';
	$importtxt = @implode('', file($importfile));
	$pluginarray = getimportdata('DZF! Plugin', $importtxt);
	if(plugininstall($pluginarray)) {
		if(!empty($pluginarray['installfile']) && file_exists(DZF_ROOT.'./source/plugin/'.$pluginid.'/'.$pluginarray['installfile'])) {
			@include_once DZF_ROOT.'./source/plugin/'.$pluginid.'/'.$pluginarray['installfile'];
		}
	}
}

?>