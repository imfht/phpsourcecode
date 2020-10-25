<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/kuaiqianccb.php');
$kuaiqianccb = new KuaiqianCcb();
$redirectLink = '';
$kuaiqianccb->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>