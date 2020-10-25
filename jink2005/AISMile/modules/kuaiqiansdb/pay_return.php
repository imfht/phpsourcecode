<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/kuaiqiansdb.php');
$kuaiqiansdb = new KuaiqianSdb();
$redirectLink = '';
$kuaiqiansdb->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>