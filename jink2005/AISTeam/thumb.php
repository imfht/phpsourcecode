<?php
define('CL_ROOT', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR));

function getArrayVal(array $array, $name)
{
    if (array_key_exists($name, $array))
    {
        return $array[$name];
    }
}
error_reporting(0);
$pic = getArrayVal($_GET,"pic");
$height = getArrayVal($_GET,"height");
$width = getArrayVal($_GET,"width");
include(CL_ROOT . "/include/class.hft_image.php");
$imagehw = GetImageSize($pic);
$imagewidth = $imagehw[0];
$imageheight = $imagehw[1];
$myThumb = new hft_image(CL_ROOT . "/" . $pic);
$myThumb->jpeg_quality = 80;

if (!isset($height))
{
	$ratio =  $imageheight / $imagewidth;
	$height = $width * $ratio;

	$height = round($height);
}

if (!isset($width))
{
	$ratio = $imagewidth / $imageheight;
    $width = $height * $ratio;
}

$myThumb->resize($width, $height, 0);

HEADER("Content-Type: image/jpeg");
$myThumb->output_resized("");

?>