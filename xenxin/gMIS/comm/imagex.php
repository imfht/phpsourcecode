<?php

# verifycode generation
# wadelau@ufqi.com
# Tue Jul 24 18:37:49 CST 2012
# Thu Apr 18 15:18:19 CST 2013
# Thu Sep  5 09:17:54 CST 2013
# Mon Nov  4 09:03:36 CST 2013
# Mon Feb 17 08:40:23 CST 2014
# Thu Feb 20 11:02:01 CST 2014
# implemented by self session, Wed, 8 Mar 2017 21:47:40 +0800

if(!defined('__ROOT__')){
    define('__ROOT__', dirname(dirname(__FILE__)));
}
require_once(__ROOT__."/inc/config.class.php");
require_once(__ROOT__."/class/base62x.class.php");

//- default is output an img
if(true){
    $verifycode = substr($md5=Base62x::encode(md5(($i=$_REQUEST['i'])
            .$_CONFIG['sign_key']
            .($d=substr(date('YmdHi', time()-date('Z')),0,11))
            ).$_CONFIG['appchnname']), 1, 4); # same as extra/signupin
    $charc = strlen($verifycode);

	$width = 90;
	$height = 30;
	$im = imagecreatetruecolor($width, $height);
	$bg = imagecolorallocate($im, rand(135,255), rand(125,255), rand(115,255));
	imagefill($im, rand(10,intval($width/2)), rand(10,intval($height/3)), $bg);
	#imagefilledellipse($im, 80, 40, 80, 40, $bg);
	#imagecolortransparent($im, $bg);

	$codeArr = str_split($verifycode);
	$fontspace = rand(7,10);
	$startPos = $startPos2 = rand(10, $width-($charc*$fontspace)) - 15;
	if($startPos < $width/3){ $startPos = $width/3; }
	$_x = array(1, 0, 1, 0, -1, -1, 1, 0, -1); # up to 8 chars 
	$_y = array(0, -1, -1, 0, 0, -1, 1, 1, 1); 
	$i = 0;
	$bg = imagecolorallocate($im, rand(135,255), rand(125,255), rand(115,255));
	imagefilledellipse($im, $startPos*3/2, 28, 80, 30, $bg);
	$textcolor = imagecolorallocate($im, rand(0,200), rand(10,200), rand(5, 200));
	foreach($codeArr as $k=>$v){
		$y = rand(0,intval($height/2)) + 1;
		$fontsize = rand(4, 10);
		$fontspace = rand(7, 10);
		imagestring($im, $fontsize, $startPos, $y, $v, $textcolor);
		imagestring($im, $fontsize, $startPos+$_x[$i], $y+$_y[$i], $v, $textcolor);
		#imagestring($im, (rand(2,7)), $startPos+$_x[$i], 1+rand(0,6)+$_y[$i], $v, $textcolor);
		#imagettftext($im, 20, 0, 10, 20, $textcolor, "", $v);
		$startPos += $fontspace;
		$i++;
	}

	$noisyline = 1;
	for($j=0; $j<$noisyline; $j++){
		#imageline($im, rand(1,$startPos2), rand(2, $height),  rand($startPos2, $width), rand(2, $height) , $textcolor);
		imagearc($im, rand(4,$startPos2)+40, rand(10, $height),  rand($startPos2, $width), rand(5, $height), 50, 15 , $textcolor);
		$textcolor = imagecolorallocate($im, rand(10,240), rand(0,240), rand(5, 240));
	}
	header('Content-Type: image/png');
	imagepng($im);
	imagedestroy($im);

}

# resize image by GD functions
# wadelau@ufqi.com, Thu Jan 28 22:04:14 CST 2016
# return resized image
# $toWidth: int, 10~10000 ?
# $percentNum: float, 0~1
# e.g. $destFile = resizeImage($srcFile, 1024);
# e.g. $destFile = resizeImage($srcFile, 0, 0.55);
function resizeImage($srcFile, $toWidth, $percentNum=1, $destQuality=85){

	$keepSame = 1; # 0 for cutting edges, 1 for not cutting
	$edgeCutRate = 7; # 2~10, 10 for least edge-cutting...  
	$centerRate = 1.5; # 1~5, 5 for largest leaving center...

	$srcInfo = getimagesize($srcFile); # http://www.php.net/getimagesize
	$srcWidth = $srcInfo[0];
	$srcHeight = $srcInfo[1];
	$srcType = $srcInfo[2];

	if($toWidth == 0){
		if($percentNum == 1 || $percentNum == 0){
			debug($toWidth, "toWidth-percentNum-eRror");
		}
		else{ $toWidth = $srcWidth * $percentNum; }	
	}
	else if($percentNum == 1){
		$percentNum = $toWidth / $srcWidth;
	}
	$toHeight = (int)$srcHeight * ($toWidth/$srcWidth);

	$src_x_pos = 0;
	if(1 && $srcWidth > $toWidth){
		$src_x_pos = ($srcWidth - $toWidth) / $edgeCutRate;
		$srcWidth -= $src_x_pos * $centerRate;
	}
	else{
		#$toWidth = $srcWidth; # for enlarge /scale up
	}
	$src_y_pos = 0;
	if(1 && $srcHeight > $toHeight){
		$src_y_pos = ($srcHeight - $toHeight) / $edgeCutRate;
		$srcHeight -= $src_y_pos * $centerRate;
	}
	else{
		#$toHeight = $srcHeight; 
	}
	$dest_x_pos = 0; $dest_y_pos = 0;
	#debug($src_x_pos.",".$src_y_pos.",".$percentNum);

	$srcImage = null;
	if($srcType == 1){ # 'image/gif')
		$srcImage = imagecreatefromgif($srcFile);
	}
	else if($srcType == 2){ # 'image/jpeg'
		$srcImage = imagecreatefromjpeg($srcFile);
	}
	elseif($srcType == 3){ # 'image/png'
		$srcImage = imagecreatefrompng($srcFile);
	}
	elseif($srcType == 17){ # 'image/webp', still unavailable
		$srcImage = imagecreatefrompng($srcFile);
	}

	$lastDot = strrpos($srcFile, '.');
	$destFile = substr($srcFile, 0, $lastDot).'_rs_'.$toWidth.'_'.$percentNum.'.'.substr($srcFile, $lastDot+1, strlen($srcFile));				
	if($keepSame == 0){
		$destImage = imagecreatetruecolor($toWidth, $toHeight);
		imagecopyresampled($destImage, $srcImage, 
			$dest_x_pos, $dest_y_pos, $src_x_pos, $src_y_pos, 
				$toWidth, $toHeight, $srcWidth, $srcHeight);
	}
	else{
		$destImage = imagescale($srcImage, $toWidth, $toHeight);
	}

	$destQuality = $destQuality * $percentNum;
	$destQuality = $destQuality > 100 ? 100 : $destQuality;
	if($srcType == 1){
		imagegif($destImage, $destFile, $destQuality);
	}
	else if($srcType == 2){ 
		imagejpeg($destImage, $destFile, $destQuality);
	}
	elseif($srcType == 3){ 
		imagepng($destImage, $destFile, $destQuality);
	}
	elseif($srcType == 17){ 
		imagewebp($destImage, $destFile, $destQuality);
	}
	
	return $destFile;
	
}

exit();

?>
