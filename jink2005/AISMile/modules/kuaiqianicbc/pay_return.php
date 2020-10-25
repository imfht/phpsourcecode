<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/kuaiqianicbc.php');
$kuaiqianicbc = new KuaiqianIcbc();
$redirectLink = '';
$kuaiqianicbc->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>