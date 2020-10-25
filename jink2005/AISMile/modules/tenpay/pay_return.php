<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/tenpay.php');
$tenpay = new Tenpay();
$redirectLink = '';
$tenpay->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>