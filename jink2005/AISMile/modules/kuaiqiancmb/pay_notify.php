<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/kuaiqiancmb.php');
$kuaiqiancmb = new KuaiqianCmb();
if($kuaiqiancmb->analyzeReturn())
{
	echo "success";
}

?>