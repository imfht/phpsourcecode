<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/tenpayc2c.php');
$tenpayc2c = new TenpayC2c();
$redirectLink = '';
$tenpayc2c->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>