<?php
require("./database.php");
if(empty($_SESSION['momocms_admin'])){
	header("Location:./index.php");	
	exit;
}
if($_SESSION['momocms_isAdmin']==1){
$sql="select * from ".DB_PREFIX."product_sub where id=".intval($_POST['id']);
$query=$db->query($sql);
$arr=$query->fetch();
$pic=$arr['pic'];

if (($_FILES["pic"]["type"] == "image/gif")
|| ($_FILES["pic"]["type"] == "image/jpeg")
|| ($_FILES["pic"]["type"] == "image/png")
|| ($_FILES["pic"]["type"] == "image/pjpeg"))
  {
  if ($_FILES["pic"]["error"] > 0){
    echo "Return Code: " . $_FILES["pic"]["error"] . "<br />";
    }else{
    	if(!is_dir("./upload/shows")){
    		mkdir("./upload/shows");
    	}
    	@unlink($pic);
    	 $pos = strrpos($_FILES["pic"]["name"],".");
    	 $back = substr($_FILES["pic"]["name"],$pos);
    	 $_FILES["pic"]["name"] = time()."_0".$back;
      move_uploaded_file($_FILES["pic"]["tmp_name"],
      "./upload/shows/".  $_FILES["pic"]["name"]);
      $pic="./upload/shows/".  $_FILES["pic"]["name"];
    }
  }

$_POST=array_map("htmlspecialchars",$_POST);
$_POST=array_map("addslashes",$_POST);
$db->exec("update ".DB_PREFIX."product_sub set
					name ='".$_POST['producttitle']."',
					pic = '".$pic."',
					category = '".intval($_POST['category'])."',
					description = '".$_POST['productdesc']."',
					sort = '".$_POST['sort']."' where id=".intval($_POST['id']));

echo '<script>
parent.document.getElementById("successMsg").style.display="block";
setTimeout(function(){
parent.window.location.href="./detail_product.php?id='.intval($_POST['category']).'";
},1500);
</script>';
}
?>
