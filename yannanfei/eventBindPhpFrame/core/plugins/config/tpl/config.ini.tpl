<?php
$config=array();
$local_server='{{server}}';
$config['app_site_url']='http://'.$local_server.'/{{app_name}}';  //app模块url
$config['image_site_url']='http://'.$local_server.'/{{app_name}}/resource/common/images'; //图片文件url
$config['resource_site_url']='http://'.$local_server.'/{{app_name}}/resource'; //资源文件url
$config['upload_site_url']='http://'.$local_server.'/uploads'; //资源文件url


$config['db']['master']['dbhost']       ='127.0.0.1'; //主数据库写，如果没有从数据库也从主数据库读
$config['db']['master']['dbport']       = '3306';
$config['db']['master']['dbuser']       = 'root';
$config['db']['master']['dbpwd']        = '';
$config['db']['master']['dbname']       = '';//正式库meirongu  测试库：meirong
$config['db']['master']['dbcharset']    = 'UTF-8';
$config['db']['master']['dbprefix']    =  '';
return $config;