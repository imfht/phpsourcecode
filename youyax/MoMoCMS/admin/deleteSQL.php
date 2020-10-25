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
	     @unlink($file_dir.$file_name);
	 	}
echo '<script>
parent.document.getElementById("successMsg").style.display="block";
setTimeout(function(){
parent.window.location.href="./backupSQL.php";
},1500);
</script>';
}
?>
