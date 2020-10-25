<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/cncard.php');
$cncard = new Cncard();
if($cncard->analyzeReturn())
{
	echo "success";
}

?>