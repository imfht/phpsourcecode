<?php
session_start();
/* a few 'if' */
if (empty($_SESSION['state'])) die('empty session');
if ($_SESSION['state'] != $_GET['state']) die('state not match session');
$_SESSION['state']=NULL;

require_once('./iauth_getuid.php');
$verifier = $_GET['verifier'];
$pTmp = iauth_login( $verifier,$_GET['state'] );
$uid = $pTmp['uid'];
$accessToken = $pTmp['token'];
echo $uid;
echo '<br />';
echo $accessToken;

?>