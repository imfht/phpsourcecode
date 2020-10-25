<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/kuaiqianbcom.php');
$kuaiqianbcom = new KuaiqianBcom();
$redirectLink = '';
$kuaiqianbcom->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>