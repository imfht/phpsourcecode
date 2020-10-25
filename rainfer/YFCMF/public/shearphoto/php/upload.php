<?php
header('Content-type:text/html;charset=utf-8');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
require("shearphoto.config.php");
require("shearphoto.up.php");
if (!move_uploaded_file($_FILES['UpFile']['tmp_name'], $UpFile['file_url'])) {
    HandleError('后端获取不到文件写入权限。原因：move_uploaded_file()函数-无法写入文件');
}
$UpFile['file_url']=str_replace(array(ShearURL,"\\"),array("","/"),$UpFile['file_url']);
echo('{"success":"'.$UpFile['file_url'].'"}');
?>