<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/yeepay.php');
$yeepay = new Yeepay();
$redirectLink = '';
$yeepay->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>