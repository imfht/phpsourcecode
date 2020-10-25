<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/yeepayszx.php');
$yeepayszx = new YeepaySzx();
if($yeepayszx->analyzeReturn())
{
	echo "success";
}

?>