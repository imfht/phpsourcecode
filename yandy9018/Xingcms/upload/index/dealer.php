<?php

if (!defined('APP_IN')) exit('Access Denied');
$tpl ->assign('menustate',14);
if(!isset($_COOKIE['city']) or empty($_COOKIE['city'])){
$_COOKIE['city'] = 0;
}
$tpl->assign( 'comdealer',get_comshop($_COOKIE['city']) );
$array_dealer_category = arr_dealer_category();
$tpl ->assign('dealer_category',$array_dealer_category);
$where = "isdealer=2 and ischeck=1";
if(!empty($_GET['s'])){
$tpl ->assign('shoptype',intval($_GET['s']));
$where .= " and shoptype=".intval($_GET['s']);
}
if($_COOKIE['city']!=0){
$where .= " and cid=".$_COOKIE['city'];
}
include(INC_DIR .'Page.class.php');
$Page = new Page($db ->tb_prefix .'member',$where,'*','20','id desc');
$listnum = $Page ->total_num;
$list = $Page ->get_data();
$button_basic = $Page ->button_basic();
$button_select = $Page ->button_select();
$tpl ->assign('button_basic',$button_basic);
$tpl ->assign('button_select',$button_select);
$tpl ->assign('dealerlist',$list);
$tpl ->display('default/'.$settings['templates'] .'/dealer.html');

?>