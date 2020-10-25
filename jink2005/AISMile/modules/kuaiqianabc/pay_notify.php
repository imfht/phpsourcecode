<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/kuaiqianabc.php');
$kuaiqianabc = new KuaiqianAbc();
if($kuaiqianabc->analyzeReturn())
{
	echo "success";
}

?>