<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/kuaiqian.php');
$kuaiqian = new Kuaiqian();
if($kuaiqian->analyzeReturn())
{
	echo "success";
}

?>