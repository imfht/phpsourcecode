<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/yeepay.php');
$yeepay = new Yeepay();
if($yeepay->analyzeReturn())
{
	echo "success";
}

?>