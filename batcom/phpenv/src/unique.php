<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>黄涛word处理程序</title>
</head>
<body>
<?php
//将汉字分割为字符串
function math($string,$code ='UTF-8'){
	if ($code == 'UTF-8') {
		$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
	} else {
		$pa = "/[\x01-\x7f]|[\xa1-\xff][\xa1-\xff]/";
	}
	preg_match_all($pa, $string, $t_string);
	$math="";
	foreach($t_string[0] as $k=>$s){
		$math[]=$s;
	}
	return $math;
}
error_reporting(0);
if($_POST['submit']){
    $arr=file($_FILES['file']['tmp_name']);
    $bword = $_POST['bword'];
    foreach ($arr as $line){
    	$lineArray = math(trim($line));
    	sort($lineArray);
    	$result[$line] = implode('', $lineArray);
    }
    var_dump($result);die;
    $result = array_unique($result);
    

    echo implode('<br>',array_keys($result));die;
}
?>

<form name="word" action="" method="post" enctype="multipart/form-data">
    <label for="file">A文件:</label>
    <input type="file" name="file" id="file" />
    <br />
    <input type="submit" name="submit" value="提交查看结果" />

</form>

</body>
</html>
