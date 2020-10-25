<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/kuaiqiancmb.php');
$kuaiqiancmb = new KuaiqianCmb();
$redirectLink = '';
$kuaiqiancmb->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>