<?php

error_reporting(0);
include('kcaptcha.php');
session_start();
$captcha = new KCAPTCHA();
$_SESSION['authcode'] = $captcha->getKeyString();

?>