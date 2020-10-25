<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>黄涛word处理程序</title>
</head>
<body>
<?php
error_reporting(0);
if($_POST['submit']){
    $arr=file($_FILES['file']['tmp_name']);
    $bword = $_POST['bword'];
    foreach ($arr as $line){
        if(strstr($line, $bword)){
            $result[] = $line;
        }else{
            $result[] = $line.' '.$bword;
        }
    }

    echo implode('<br>',$result);die;
}
?>

<form name="word" action="" method="post" enctype="multipart/form-data">
    <label for="file">A文件:</label>
    <input type="file" name="file" id="file" />
    <br />
    <label for="file">B词语:</label>
    <input type="text" name="bword" value=""><br>
    <input type="submit" name="submit" value="提交查看结果" />

</form>

</body>
</html>
