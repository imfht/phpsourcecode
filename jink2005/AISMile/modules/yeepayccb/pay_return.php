<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/yeepayccb.php');
$yeepayccb = new YeepayCcb();
$redirectLink = '';
$yeepayccb->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>