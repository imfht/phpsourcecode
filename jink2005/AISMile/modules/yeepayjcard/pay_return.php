<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/yeepayjcard.php');
$yeepayjcard = new YeepayJcard();
$redirectLink = '';
$yeepayjcard->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>