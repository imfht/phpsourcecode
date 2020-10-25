<?php
require '../includes/init.php';

switch($_GET['action']){
	case 'site':
		//保存提交过来的站点配置信息到文件中
		if($_POST['action'] == 1){
			$new_config = array();
			$new_config['title'] = $_POST['site_title'];
			$new_config['domain'] = $_POST['site_domain'];
			$new_config['logo'] = $_POST['site_logo'];
			$new_config['keywords'] = $_POST['site_keywords'];
			$new_config['description'] = $_POST['site_description'];
			$new_config['share'] = $_POST['site_share'];
			$new_config['count'] = $_POST['site_count'];
			$new_config['status'] = $_POST['site_status'];
			
		}
		$site = true;

		break;
	case 'config':

		break;
}

include TEMP_DIR.'admin/'.ADMIN_THEME.'/system.html';
?>