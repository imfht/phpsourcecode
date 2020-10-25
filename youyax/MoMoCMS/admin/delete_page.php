<?php
require("./database.php");
if(empty($_SESSION['momocms_admin'])){
	header("Location:./index.php");	
	exit;
}
if($_SESSION['momocms_isAdmin']==1){
$db->exec("delete from ".DB_PREFIX."pages where id=".intval($_GET['id']));
echo '<script>
parent.window.location.href="./page.php";
</script>';
}
?>
