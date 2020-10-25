<?php

session_start();

$Captcha = isset($_GET['c']) ? $_GET['c'] : '';

if (preg_match('/^[A-Za-z0-9_]+$/', $Captcha)) {

    require_once dirname(__FILE__).'/includes/lib/Captcha.php';

    new MathCaptcha(100, 24, $Captcha);
}
?>