<?php
header("content-type:text/html; charset=utf-8");
$file=empty($_GET['file'])?'':$_GET['file'];
if($file && strpos('a'.$file,'20')==1 && substr($file,-4,4)=='.log'){
    $content= file_get_contents('./success_proxy/'.$file);
    echo nl2br($content);
    exit;
}
$list=scandir('./success_proxy');
foreach($list as $v){
    if(strpos('a'.$v,'20')==1){
        echo "<a href='?file=".$v."'>".$v."</a><br>";
    }
}
?>
