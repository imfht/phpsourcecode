<?php header('Content-type: application/x-javascript;charset=utf-8');
/*
文字统一调用文件v150415	By:shileiye
调用方法：<script>showtext("zj");</script>
说明：参数可选
*/
require 'config.php';
//PHP数组转JS数组
echo "var info=new Array();\n";
foreach($info as $key=>$value){
	echo "info['$key']='$value';\n";
}
?>
function showtext(wz){
	document.write(info[wz]);
}