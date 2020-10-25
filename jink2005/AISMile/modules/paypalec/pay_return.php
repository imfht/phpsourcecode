<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/paypalec.php');
$paypalec = new PaypalEc();
$redirectLink = '';
$paypalec->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>