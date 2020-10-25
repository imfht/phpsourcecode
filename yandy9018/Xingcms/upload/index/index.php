<?php

if (!defined('APP_IN')) exit('Access Denied');
$tpl ->assign('menustate',1);
if(!isset($_COOKIE['city']) or empty($_COOKIE['city'])){
$_COOKIE['city'] = 0;
}
$arr_b = arr_brand_recom();
$tpl ->assign('arr_brand',$arr_b);
$tpl ->assign('noticelist',get_comnews(8,4));
$tpl ->assign('filmlist',get_filmstrip(1));
$carlist = array();
$carlist['todaycar'] = get_todaycar($_COOKIE['city']);
if($settings['version']==2 or $settings['version']==3){
$carlist['sjcar'] = get_carlist($_COOKIE['city'],"isrecom=1 and issell=0 and isshow=1 and uid in( select id from ".$db_config['TB_PREFIX'] ."member where isdealer=2)",'8','listtime desc');
$carlist['newcar'] = get_carlist($_COOKIE['city'],"issell=0 and isshow=1 and p_mainpic!='' ",'20','p_id desc');
$carlist['grcar'] = get_carlist($_COOKIE['city'],"isrecom=1 and issell=0 and p_mainpic!='' and isshow=1 and (uid in( select id from ".$db_config['TB_PREFIX'] ."member where isdealer=1) or uid=0)",'8','listtime desc');
$tpl ->assign('car_list',$carlist);
$tpl ->assign('comdealer',get_comshop($_COOKIE['city']));
$tpl ->assign('hotdealer',get_hotshop($_COOKIE['city']));
}
else{
$carlist['grcar'] = get_carlist($_COOKIE['city'],"isrecom=1 and issell=0 and p_mainpic!='' and isshow=1",'8','listtime desc');
}
$tpl ->assign('car_list',$carlist);
$newslist = array();
$newslist['1'] = get_comnews(1,8);
$newslist['2'] = get_comnews(2,8);
$newslist['3'] = get_comnews(3,8);
$newslist['4'] = get_comnews(4,8);
$tpl ->assign('newslist',$newslist);
$picnewslist = array();
$picnewslist['1'] = get_comnews(1,2);
$picnewslist['2'] = get_comnews(2,2);
$picnewslist['3'] = get_comnews(3,2);
$picnewslist['4'] = get_comnews(4,2);
$tpl ->assign('picnewslist',$picnewslist);
$notice = get_comnews(5,4);
$tpl ->assign('noticelist',$notice);
$tpl ->assign('link_list',get_flink());
$tpl ->assign('hotkeywordlist',get_bottomkeywords());
$tpl ->display('default/'.$settings['templates'] .'/index.html');

?>