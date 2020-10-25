<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
return [

    'upload_image_size'=>5000,
    'upload_file_size'=>0,
    'upload_image_ext'=>'jpeg,bmp,gif,png,jpg,ico',
    'upload_file_ext'=>'xml,xls,doc,zip,rar,text,ico',
    'upload_path'   =>ROOT_PATH . 'public' . DS . 'uploads',##生成时候调用
    'upload_path_view'=>'/public' . DS ,##生成后.返出的时候调用
    'upload_thumb_water'=>0,##1为开启
    'upload_thumb_water_pic'=>'',##path
    'is_oss'        =>'0'##是否为OSS上传











];    