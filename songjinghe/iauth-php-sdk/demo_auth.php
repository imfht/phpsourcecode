<?php
session_start();

if (empty($_SESSION['state'])) die('empty session,try <a href="demo_session.php">demo_session.php</a>');
if ($_SESSION['state'] != $_GET['state']) die('state not match session,try <a href="demo_session.php">demo_session.php</a>');
$_SESSION['state']=NULL;


require_once('./iauth_auth.php');

$verifier = $_GET['verifier'];
$state = $_GET['state'];
var_dump(iauth_auth( $verifier, $state ));

echo "您现在已经授权成功，可以调用API来获取数据了。以下页面来自<a href='demo_getdata.php'>demo_getdata.php</a>，您也可以直接访问该页面<hr />";
require_once('demo_getdata.php');
?>
