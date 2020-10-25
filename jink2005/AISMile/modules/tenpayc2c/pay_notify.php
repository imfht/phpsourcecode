<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/tenpayc2c.php');
$tenpayc2c = new TenpayC2c();
if($tenpayc2c->analyzeReturn())
{
	echo "success";
}

?>