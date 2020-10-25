<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/kuaiqian.php');
$kuaiqian = new Kuaiqian();
$redirectLink = '';
$kuaiqian->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>