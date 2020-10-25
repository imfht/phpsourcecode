<?php
require("./database.php");
if(empty($_SESSION['momocms_admin'])){
	header("Location:./index.php");	
	exit;
}
if($_SESSION['momocms_isAdmin']==1){
$db_server = DB_HOST; // your MySQL server - localhost will normally suffice
$db = DB_NAME; // your MySQL database name
$mysql_username = DB_USER;  // your MySQL username
$mysql_password = DB_PSW;  // your MySQL password
require("./phpmysqlautobackup/run.php");
echo '<script>
parent.document.getElementById("successMsg").style.display="block";
setTimeout(function(){
parent.window.location.href="./backupSQL.php";
},1500);
</script>';
}
?>
