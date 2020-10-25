<?php
require("./database.php");
if(empty($_SESSION['momocms_admin'])){
	header("Location:./index.php");	
	exit;
}
if($_SESSION['momocms_isAdmin']==1){
 $file_name=base64_decode($_GET['file']);
 $file_dir = "./phpmysqlautobackup/backups/";
        if(file_exists($file_dir.$file_name)){
	       header("Content-Type:text/plain");  
		   header("Accept-Ranges:bytes");
		   header("Content-Disposition:attachment;filename=".$file_name);
		   $file = fopen($file_dir.$file_name,"r");
		   echo fread($file,filesize($file_dir.$file_name));
		   fclose($file);
	 	}
}
?>
