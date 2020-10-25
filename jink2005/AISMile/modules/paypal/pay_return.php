<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/paypal.php');
$paypal = new Paypal();
$redirectLink = '';
$paypal->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>