<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/yeepaycmbchina.php');
$yeepaycmbchina = new YeepayCmbchina();
$redirectLink = '';
$yeepaycmbchina->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>