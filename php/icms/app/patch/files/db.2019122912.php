<?php
@set_time_limit(0);
defined('iPHP') OR require (dirname(__FILE__).'/../../../iCMS.php');

// patch::$check_login = 0;//debug

return patch::upgrade(function(){
  if(strpos(iCMS::$config['FS']['dir_format'], '{')===false){
    $dir_format =  str_replace(
      array('date:Ymd','md5:0,2','md5:2,3','EXT','Y','y','m','n','d','j','H','i','s'),
      array('{date:Ymd}','{md5:0,2}','{md5:2,3}','{EXT}','{Y}','{y}','{m}','{n}','{d}','{j}','{H}','{i}','{s}'),
      iCMS::$config['FS']['dir_format']
    );
    iCMS::$config['FS']['dir_format'] = str_replace('}{', '', $dir_format);
    $config = iCMS::$config['FS'];
    config::set($config,'FS',0,false);
    config::cache();
    $msg = '附件配置更新完成';
  }
  return $msg;
});
