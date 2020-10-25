<?php
function SetupString()
{
	$error_reporting = ini_get('error_reporting');
	error_reporting($error_reporting &  ~E_NOTICE);

	require("./string.php");
	
	if ($error_mesg != "") {
		return -1;
	} else {
		return 0;
	}
}

$error = SetupString();
?>

