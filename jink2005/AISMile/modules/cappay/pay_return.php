<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/cappay.php');
$cappay = new Cappay();
$redirectLink = '';
$cappay->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>