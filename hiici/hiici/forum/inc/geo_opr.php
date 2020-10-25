<?php 

$geo = @filter_var($_POST['geo'], FILTER_SANITIZE_STRING);
if (!empty($geo)) {
	$geo = split(',', $geo);

	require_once('inc/lib/geohash.class.php');
	$geohash = new Geohash;
	$geo = substr($geohash->encode($geo[0], $geo[1]), 0, 6);
}

if (empty($icon_url)) $icon_url = get_img_url($content);   //自动提取图标URL
