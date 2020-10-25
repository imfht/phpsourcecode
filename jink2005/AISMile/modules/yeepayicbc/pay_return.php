<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/yeepayicbc.php');
$yeepayicbc = new YeepayIcbc();
$redirectLink = '';
$yeepayicbc->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>