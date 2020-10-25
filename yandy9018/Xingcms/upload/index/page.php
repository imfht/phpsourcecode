<?php

if (!defined('APP_IN')) exit('Access Denied');
$settings  = settings();
$tpl ->assign('cache',$commoncache);
$arr_p = array('1'=>'3万以下','2'=>'3-5万','3'=>'5-8万','4'=>'8-12万','5'=>'12-18万','6'=>'18-24万','7'=>'24-35万','8'=>'35-50万','9'=>'50-100万','10'=>'100万以上');
$tpl ->assign('arr_price',$arr_p);
$arr_a = array('1'=>'1年以内','2'=>'2年以内','3'=>'3年以内','4'=>'4年以内','5'=>'5年以内','6'=>'6年以内','7'=>'6年以上');
$tpl ->assign('arr_age',$arr_a);
$select_brand = select_make('',$commoncache['markbrandlist'],'请选择品牌');
$select_model= select_make('',$commoncache['modellist'],'请选择车型');
$arr_age_b = array('1'=>'1年以内','2'=>'2年以内','3'=>'3-5年','4'=>'5-8年','5'=>'8年以上');
$arr_kilometre_b = array('1'=>'1万公里以下','2'=>'2万公里以下','3'=>'3万公里以下','4'=>'5万公里以下','5'=>'10万公里以下');
$tpl ->assign('arr_age_b',$arr_age_b);
$tpl ->assign('arr_kilometre_b',$arr_kilometre_b);
$select_age = select_make('',$arr_age_b,'不限');
$select_kilometre = select_make('',$arr_kilometre_b,'不限');
$select_year = select_make('',$commoncache['yearlist'],'请选择年份');
$select_color = select_make('',$commoncache['colorlist'],'请选择颜色');
$select_month = select_make('',array('01'=>'01月','02'=>'02月','03'=>'03月','04'=>'04月','05'=>'05月','06'=>'06月','07'=>'07月','08'=>'08月','09'=>'09月','10'=>'10月','11'=>'11月','12'=>'12月'),'请选择月份','');
$select_gas = select_make('',$commoncache['gaslist'],'请选择排量');
$select_transmission = select_make('',$commoncache['transmissionlist'],'请选择变速箱');
$select_country = select_make('',array('国产'=>'国产','进口'=>'进口'),'请选择');
$tpl->assign( 'selectbrand',$select_brand );
$tpl->assign( 'selectmodel',$select_model );
$tpl ->assign('select_age',$select_age);
$tpl ->assign('select_kilometre',$select_kilometre);
$tpl ->assign('select_year',$select_year);
$tpl ->assign('select_color',$select_color);
$tpl ->assign('select_month',$select_month);
$tpl ->assign('select_gas',$select_gas);
$tpl ->assign('select_transmission',$select_transmission);
$tpl ->assign('select_country',$select_country);
$tpl->assign( 'weburl',WEB_PATH );
$tpl->assign( 'htmldir',HTML_DIR );
$tpl->assign( 'setting',$settings );
$tpl->assign( 'partlist',get_channel());
$tpl->assign( 'adminpage',ADMIN_PAGE );
?>