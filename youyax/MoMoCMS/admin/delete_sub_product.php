<?php
require("./database.php");
if(empty($_SESSION['momocms_admin'])){
	header("Location:./index.php");	
	exit;
}
if($_SESSION['momocms_isAdmin']==1){
$sql="select * from ".DB_PREFIX."product_sub where id=".intval($_GET['id']);
$query=$db->query($sql);
$arr=$query->fetch();
$pic=$arr['pic'];
if(file_exists($pic)){@unlink($pic);}
$db->exec("delete from  ".DB_PREFIX."product_sub where id=".intval($_GET['id']));
echo '<script>
parent.window.location.href="./detail_product.php?id='.$arr['category'].'";
</script>';
}
?>
