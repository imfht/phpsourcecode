<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/chinabank.php');
$chinabank = new Chinabank();
$redirectLink = '';
$chinabank->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>