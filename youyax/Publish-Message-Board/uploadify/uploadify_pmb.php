<?php
session_id($_REQUEST["sessid"]);
session_start();
	if(!is_dir("../imap/example/attachments/".intval($_POST['pnum']))){
				mkdir("../imap/example/attachments/".intval($_POST['pnum']), 0777);
			}
$targetFolder = "../imap/example/attachments/".intval($_POST['pnum']); // Relative to the root			
if (!empty($_FILES) && $_POST['token'] == $_SESSION['token']) {
	 $pos=strrpos($_FILES['Filedata']['name'],"."); 
	 $font=substr($_FILES['Filedata']['name'],0,$pos)."_".time();
    $_FILES['Filedata']['name']=preg_replace("/\s+/","-",$font).substr($_FILES['Filedata']['name'],$pos);
    $_FILES['Filedata']['name']=preg_replace("/[\x{4e00}-\x{9fa5}]+/u",'',$_FILES['Filedata']['name']);
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetFile=$targetFolder. '/' . $_FILES['Filedata']['name'];
	$fileTypes = array('jpg','jpeg','gif','png','bmp','zip','pdf','txt','doc','xlsx','xls','wps','rar'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);	
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		echo $_FILES['Filedata']['name'];
	} else {
		echo '无效的文件格式';
	}
}
?>