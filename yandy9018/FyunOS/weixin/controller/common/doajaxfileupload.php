<?php
require_once('upyun.class.php');
class ControllerCommonDoajaxfileupload extends Controller {
	public function index() {
    $error = "";
    $msg = "";
    $path = pathinfo($_FILES['fileToUpload']['name']);


    $wdir = '/'.$_GET['m'].'/p_'.time().'.'.$path['extension'];
    $httpurl = "http://fyunimage.b0.upaiyun.com".$wdir;
    $upyun = new UpYun('fyunimage', 'fyunimage', 'yandy000');
  if(@filesize($_FILES['fileToUpload']['tmp_name'])>1048576){
   die("{error:'上传图片超过1M',msg:''}");
  }
try {
    $fh = fopen($_FILES["fileToUpload"]["tmp_name"], 'rb');
    $rsp = $upyun->writeFile($wdir, $fh, True);   // 上传图片，自动创建目录
    fclose($fh);
    echo "{error:'',msg:'".$httpurl."'}";
}
catch(Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
}
	
  }
}
?>