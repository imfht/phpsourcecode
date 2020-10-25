<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/yeepayccb.php');
$yeepayccb = new YeepayCcb();
if($yeepayccb->analyzeReturn())
{
	echo "success";
}

?>