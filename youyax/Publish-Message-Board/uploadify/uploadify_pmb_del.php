<?php
session_id($_REQUEST["sessid"]);
session_start();
if($_POST['token'] == $_SESSION['token']) {
$val=filesize("../imap/example/attachments/".intval($_POST['pnum'])."/".str_replace(array('./','../'),'',$_POST['name']));
$array=array();
$array['name']=$_POST['name'];
$array['size']=filesize("../imap/example/attachments/".intval($_POST['pnum'])."/".str_replace(array('./','../'),'',$_POST['name']));
@unlink("../imap/example/attachments/".intval($_POST['pnum'])."/".str_replace(array('./','../'),'',$_POST['name']));
echo json_encode($array);
}
?>