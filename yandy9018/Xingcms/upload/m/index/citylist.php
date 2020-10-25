<?php

if (!defined('APP_IN')) exit('Access Denied');
$list = $db ->row_select('area',"parentid=-1",'id,name',0,'orderid asc');
foreach($list as $key =>$value){
$citylist = $db ->row_select('area',"parentid=".$value['id'],'id,name',0,'orderid asc');
$list[$key]['citylist'] = $citylist;
}
$tpl->assign( 'arealist',$list );
$tpl ->display('m/city.html');
?>