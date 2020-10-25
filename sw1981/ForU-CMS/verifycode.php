<?php
include './library/inc.php';
include_once LIB_PATH . 'cls.verifycode.php';

$_vc = new VerifyCode(VERIFYCODE_WIDTH, VERIFYCODE_HEIGHT);
$_vc->doimg();
$_SESSION['verifycode'] = $_vc->getCode();
