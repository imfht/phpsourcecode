<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/ips.php');
$ips = new Ips();
$redirectLink = '';
$ips->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>