<?php

if (!defined('APP_IN')) exit('Access Denied');
$settings  = settings();
$tpl->assign( 'setting',$settings );
$tpl ->assign('cache',$commoncache);
$select_year = select_make('',$commoncache['yearlist'],'请选择年份');
$select_month = select_make('',array('01'=>'01月','02'=>'02月','03'=>'03月','04'=>'04月','05'=>'05月','06'=>'06月','07'=>'07月','08'=>'08月','09'=>'09月','10'=>'10月','11'=>'11月','12'=>'12月'),'请选择月份','');
$tpl ->assign('select_year',$select_year);
$tpl ->assign('select_month',$select_month);
$tpl->assign( 'webdomain',WEB_DOMAIN );

?>