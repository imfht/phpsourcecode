<?php
require '../includes/init.php';

$friendLinkModel = new FriendlinkModel();

switch($_GET['action']){
	case 'showFriendlink':
		$showFriendlink = true;
		$rs = $friendLinkModel->showFriendlink();
		break;
	case 'addFriendlink':
	    if ($_POST['send']){
	        $friendLinkModel->title = $_POST['title'];
	        $friendLinkModel->url = $_POST['url'];
	        $friendLinkModel->logo_url = UploadTool::up('logo_url');
	        $friendLinkModel->sort = $_POST['sort'];
	        $friendLinkModel->show_type = $_POST['show_type'];
	        if(!$friendLinkModel->addFriendlink()){
				exit('栏目添加失败！');
			}else{
				header('Location:./friendlink.php?action=showFriendlink');
			}
	    }
	    $addFriendlink = true;
		break;
	case 'deleteFriendlink':
		$friendLinkModel->id = intval($_GET['id']);
	    if ($friendLinkModel->deleteFriendlink()){
	        header('Location:./friendlink.php?action=showFriendlink');
	    }
		break;
	case 'updateFriendlink':
		$friendLinkModel->id = intval($_GET['id']);
	    $one = $friendLinkModel->oneFriendlink();
	    if ($_POST['send']){
	        $friendLinkModel->title = $_POST['title'];
	        $friendLinkModel->url = $_POST['url'];
	        $friendLinkModel->logo_url = UploadTool::up('logo_url');
	        $friendLinkModel->sort = $_POST['sort'];
	        $friendLinkModel->show_type = $_POST['show_type'];
	        if(!$friendLinkModel->updateFriendlink()){
				exit('栏目添加失败！');
			}else{
				header('Location:./friendlink.php?action=showFriendlink');
			}
	    }
	    $updateFriendlink = true;
		break;
}

include TEMP_DIR.'admin/'.ADMIN_THEME.'/friendlink.html';
