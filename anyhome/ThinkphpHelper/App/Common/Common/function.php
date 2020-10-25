<?php
//将数字转换为目录 例如  31  =   000/000/31
function numberDir($num = 0) {
	if ($num == 0) $num = date('Ymd');
    $num = sprintf("%09d", $num);
    $dir1 = substr($num, 0, 3);
    $dir2 = substr($num, 3, 2);
    $dir3 = substr($num, 5, 2);
    return $dir1.'/'.$dir2.'/'.$dir3;
}
?>