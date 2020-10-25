<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/alipay.php');
$alipay = new Alipay();
$redirectLink = '';
$alipay->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>