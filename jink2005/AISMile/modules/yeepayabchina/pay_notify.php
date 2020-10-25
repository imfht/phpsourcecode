<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/yeepayabchina.php');
$yeepayabchina = new YeepayAbchina();
if($yeepayabchina->analyzeReturn())
{
	echo "success";
}

?>