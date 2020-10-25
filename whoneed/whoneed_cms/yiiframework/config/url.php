<?php
return array(
	
    //后台管理系统
    'admin' => '/admin/site',	//后台管理

    //package
    'channel_<company:[^_]+>_<product:[^_]+>_<channel_id:\d+>_<sub_id:\d+>.html' => 'package/index',
    'channel_<company:[^_]+>_<product:[^_]+>_<channel_id:\d+>.html' => 'package/index',
    'channel_<channel_id:\d+>.html' => 'package/index',

    //packageDown
    //'package_download_<channel_id:\d+>.apk'=>'package/download'
);
