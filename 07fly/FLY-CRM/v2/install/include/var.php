<?php
$env_items = array();
$dirfile_items = array(
		array('type' => 'dir', 'path' => 'ERP/Config'),
		array('type' => 'file', 'path' => 'ERP/Config/Config.php'),
		array('type' => 'dir', 'path' => 'install'),
);

$func_items = array(
		array('name' => 'mysqli_connect'),
		array('name' => 'fsockopen'),
		array('name' => 'gethostbyname'),
		array('name' => 'file_get_contents'),
		array('name' => 'mb_convert_encoding'),
		array('name' => 'json_encode'),
		array('name' => 'curl_init'),
);