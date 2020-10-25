<?php
require("./database.php");
if(empty($_SESSION['momocms_admin'])){
	header("Location:./index.php");	
	exit;
}
if($_SESSION['momocms_isAdmin']==1){
$db->exec("delete from  ".DB_PREFIX."leave  where id=".intval($_GET['id']));
echo '<script>
window.location.href="./leave.php";
</script>';
}
?>
