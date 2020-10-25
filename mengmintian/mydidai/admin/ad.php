<?php
require '../includes/init.php';
$adModel = new AdModel();

switch($_GET['action']){
	case 'showAd':
		$showAd = true;
		$rs = $adModel->showAd();
		break;
	case 'addAd':
		$addAd = true;
		add();
		break;
	case 'deleteAd':
		$adModel->id = $_GET['id'];
		if($adModel->deleteAd()){
			exit('栏目删除成功！');
		}
		break;
	case 'updateAd':
		update();
		$one = $adModel->oneAd();
		$updateAd = true;
		break;
}

function add(){
	global $adModel;
	if($_POST['send']){
		$adModel->title = $_POST['title'];
		$adModel->url = $_POST['url'];
		$adModel->pic = $_POST['pic'];
		$adModel->content = $_POST['content'];
		$adModel->pos = $_POST['pos'];
		$adModel->start_time = $_POST['start_time'];
		$adModel->end_time = $_POST['end_time'];
		if(!$adModel->addAd()){
			exit('栏目添加失败！');
		}else{
			header('Location:./ad.php?action=showAd');
		}
	}
}

function update(){
	global $adModel;
	$adModel->id = $_GET['id'];

	if($_POST['send']){
		$adModel->title = $_POST['title'];
		$adModel->url = $_POST['url'];
		$adModel->pic = $_POST['pic'];
		$adModel->content = $_POST['content'];
		$adModel->pos = $_POST['pos'];
		$adModel->start_time = $_POST['start_time'];
		$adModel->end_time = $_POST['end_time'];
		if($adModel->updateAd()){
			exit('栏目修改成功！');
		}else{
			header('Location:./ad.php?action=updateAd');
		}
	}
}

include TEMP_DIR.'admin/'.ADMIN_THEME.'/ad.html';