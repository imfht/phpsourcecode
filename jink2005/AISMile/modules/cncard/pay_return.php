<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/cncard.php');
$cncard = new Cncard();
$redirectLink = '';
$cncard->analyzeReturn($redirectLink);
Tools::redirect($redirectLink);

?>