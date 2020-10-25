<?php
require("./database.php");
if(empty($_SESSION['momocms_admin'])){
	header("Location:./index.php");	
	exit;
}
if($_SESSION['momocms_isAdmin']==1){
$_GET=array_map("htmlspecialchars",$_GET);
$_GET=array_map("addslashes",$_GET);
$db->exec("optimize table `".$_GET['table']."`");
echo '<script>
parent.document.getElementById("successMsg").style.display="block";
setTimeout(function(){
parent.window.location.href="./backupSQL.php";
},1500);
</script>';
}
?>
