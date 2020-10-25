<?php
require("./database.php");
if(empty($_SESSION['momocms_admin'])){
	header("Location:./index.php");	
	exit;
}
if($_SESSION['momocms_isAdmin']==1){
if (($_FILES["banner"]["type"] == "image/gif")
|| ($_FILES["banner"]["type"] == "image/jpeg")
|| ($_FILES["banner"]["type"] == "image/png")
|| ($_FILES["banner"]["type"] == "image/pjpeg"))
  {
  if ($_FILES["banner"]["error"] > 0){
    echo "Return Code: " . $_FILES["banner"]["error"] . "<br />";
    }else{
    	if(!is_dir("../resource/slide/images")){
    		mkdir("../resource/slide/images");
    	}
    	 $pos = strrpos($_FILES["banner"]["name"],".");
    	 $back = substr($_FILES["banner"]["name"],$pos);
    	 $_FILES["banner"]["name"] = time()."_0".$back;
      move_uploaded_file($_FILES["banner"]["tmp_name"],
      "../resource/slide/images/".  $_FILES["banner"]["name"]);
      $pic="../resource/slide/images/".  $_FILES["banner"]["name"];
    }
  }

echo '<script>
parent.document.getElementById("successMsg").style.display="block";
setTimeout(function(){
parent.window.location.href="./banner.php";
},1500);
</script>';
}
?>
