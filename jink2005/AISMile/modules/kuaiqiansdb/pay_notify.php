<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/kuaiqiansdb.php');
$kuaiqiansdb = new KuaiqianSdb();
if($kuaiqiansdb->analyzeReturn())
{
	echo "success";
}

?>