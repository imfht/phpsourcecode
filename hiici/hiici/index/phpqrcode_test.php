<?php 


include 'inc/phpqrcode/qrlib.php'; 
// 二维码数据 
$data = 'http://www.hiici.com'; 
// 生成的文件名 
$filename = false;
// 纠错级别：L、M、Q、H 
$errorCorrectionLevel = 'L';  
// 点的大小：1到10 
$matrixPointSize = 5;  
QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize);
