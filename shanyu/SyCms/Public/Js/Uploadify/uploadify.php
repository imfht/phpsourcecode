<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
// $_POST=array(
//   'token' => 'c74499ffdb52b9d9f1a0f53030a2e0d8',
//   'times' => '1433075090',
// );
// $_FILES=array(
// 	'IfyData' => array (
// 		'name' => 'wwwww.png',
// 		'type' => 'image/png',
// 		'tmp_name' => 'D:\Program Files\wamp\tmp\phpB189.tmp',
// 		'error' => 0,
// 		'size' => 41901,
//     ),
// );



$targetFolder = '/Shanyu/Uploads'; // Relative to the root

$verifyToken = md5('SafeKey' . $_POST['times']);


if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	$tempFile = $_FILES['IfyData']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['IfyData']['name'];

	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($_FILES['IfyData']['name']);
	
	if (in_array($fileParts['extension'],$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		echo $targetFolder.'/'.$_FILES['IfyData']['name'];
	} else {
		echo 'Invalid file type.';
	}
}

?>