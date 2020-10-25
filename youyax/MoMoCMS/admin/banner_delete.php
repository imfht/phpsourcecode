<?php
require("./database.php");
if(empty($_SESSION['momocms_admin'])){
	header("Location:./index.php");	
	exit;
}
if($_SESSION['momocms_isAdmin']==1){
$_GET['file'] = str_replace(array("./","..","/"),'',$_GET['file']);
if(file_exists("../resource/slide/images/".$_GET['file'])){
	@unlink("../resource/slide/images/".$_GET['file']);
}
echo '<script>
window.location.href="./banner.php";
</script>';
}
?>
