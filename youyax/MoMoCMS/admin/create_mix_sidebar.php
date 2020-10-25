<?php
require("./database.php");
if(empty($_SESSION['momocms_admin'])){
	header("Location:./index.php");	
	exit;
}
if($_SESSION['momocms_isAdmin']==1){
	$_POST=array_map("htmlspecialchars",$_POST);
	$_POST=array_map("addslashes",$_POST);
	$db->exec("insert into ".DB_PREFIX."mix_sidebar set
						name ='".$_POST['bars']."',
						pid='".intval($_POST['bars_page'])."'");
	echo '<script>
	parent.document.getElementById("successMsg").style.display="block";
	setTimeout(function(){
	parent.window.location.href="./mix_sidebar.php";
	},1500);
	</script>';
}
?>
