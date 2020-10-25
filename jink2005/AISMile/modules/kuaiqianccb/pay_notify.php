<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/kuaiqianccb.php');
$kuaiqianccb = new KuaiqianCcb();
if($kuaiqianccb->analyzeReturn())
{
	echo "success";
}

?>