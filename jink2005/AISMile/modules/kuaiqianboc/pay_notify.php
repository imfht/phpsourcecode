<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/kuaiqianboc.php');
$kuaiqianboc = new KuaiqianBoc();
if($kuaiqianboc->analyzeReturn())
{
	echo "success";
}

?>