<?php
session_start();


require_once(dirname(__FILE__).'/iauth_config.php');
$state = GetRandomString(16);
$_SESSION['state']=$state;
header( 'Location: '.IAUTH_LOGIN_URL.'?appid='.IAUTH_APP_ID.'&state='.$state);
exit();



function GetRandomString( $len, $content='' ){
    $pTmp = base64_encode(sha1(uniqid(rand() . time() . $content, true),true));
    $pTmp = str_replace('+','r', $pTmp);
    $pTmp = str_replace('=','s', $pTmp);
    $pTmp = str_replace('/','E', $pTmp);
    return substr( $pTmp, 0, $len);
    }

?>
