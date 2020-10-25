<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/shenzhou.php');
$shenzhou = new Shenzhou();
if($shenzhou->analyzeReturn())
{
	echo "success";
}

?>