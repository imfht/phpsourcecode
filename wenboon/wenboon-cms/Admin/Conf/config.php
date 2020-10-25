<?php
$config= array(    
   //控制器_函数 文件命名格式
   'TMPL_FILE_DEPR'=>'_',
   //生成url模式
   'URL_MODEL'=>0,
   'HTML_CACHE_ON'=>0,
   'URL_ROUTER_ON' => 0,
   'TMPL_PARSE_STRING'=>array(
    '__PUBLIC__'=>__ROOT__.'/'.APP_NAME.'/Public',
    '__STATIC__'=>__ROOT__.'/Lib/Public',
  ),
);

return array_merge(include '../config.php',$config);
?>