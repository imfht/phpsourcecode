<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/yeepayicbc.php');
$yeepayicbc = new YeepayIcbc();
if($yeepayicbc->analyzeReturn())
{
	echo "success";
}

?>