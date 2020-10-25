<?php
$config=array(
    //生成url模式
     //应用__PUBLIC__代表的路径
      'TMPL_PARSE_STRING'=>array(
        '__PUBLIC__'=>__ROOT__.'/'.APP_NAME.'/Public',
        '__STATIC__'=>__ROOT__.'/Lib/Public',
      ),
);
return array_merge(include './config.php',$config);
?>