<?php

$img = imagecreatetruecolor(75, 25);
$white = imagecolorallocate($img, 255, 255, 255);
$blue = imagecolorallocate($img, 0, 100, 200);
imagefill($img, 0, 0, $blue);
$str = '';
for ($i=0;$i<4;$i++){
	$str .= rand(0,9);
}
imagestring($img, 5, 0, 0, $str, $white);
imagejpeg($img);

?>