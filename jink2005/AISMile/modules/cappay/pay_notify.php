<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/cappay.php');
$cappay = new Cappay();
if($cappay->analyzeReturn())
{
	echo "success";
}

?>