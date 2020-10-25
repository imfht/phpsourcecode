<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/yeepayabchina.php');
$yeepayabchina = new YeepayAbchina();
$redirectLink = '';
$yeepayabchina->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>