<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/paypalec.php');
$paypalec = new PaypalEc();
if($paypalec->analyzeReturn())
{
	echo "success";
}

?>