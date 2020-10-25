<?php
$fp = fopen("http://bbs.destoon.com/","r"); //以只读的方式打开某个站点下的文件
foreach($http_response_header as $info) //对$http_response_header的文件信息头进行遍历循环
echo $info."<br>"; //最后输出各条记录信息
?>