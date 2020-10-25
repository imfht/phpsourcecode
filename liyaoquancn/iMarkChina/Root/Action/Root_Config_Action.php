<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/ 
session_start();
date_default_timezone_set('PRC');
include 'Root_Hackdone_Action.php';
$display_info = false;
if (isset($_POST['save'])) {
    $user_name_changed = $_POST['user_name'] != $Mark_Config_Action['user_name'];
    $Mark_Config_Action['site_name'] = $_POST['site_name'];
    $Mark_Config_Action['site_desc'] = $_POST['site_desc'];
    $Mark_Config_Action['site_key'] = $_POST['site_key'];
    $Mark_Config_Action['site_link'] = $_POST['site_link'];
    $Mark_Config_Action['user_nick'] = $_POST['user_nick'];
    $Mark_Config_Action['user_name'] = $_POST['user_name'];
    $Mark_Config_Action['copy_right'] = $_POST['copy_right'];
    $Mark_Config_Action['root_link'] = $_POST['root_link'];
    $Mark_Config_Action['site_mumber'] = $_POST['site_mumber'];
    $Mark_Config_Action['style'] = $_POST['style'];
    $Mark_Config_Action['nametwo'] = $_POST['nametwo'];
    $Mark_Config_Action['fdlinks'] = $_POST['fdlinks'];
    $Mark_Config_Action['write'] = $_POST['write'];
    $Mark_Config_Action['root_file'] = $_POST['root_file'];
    $Mark_Config_Action['comment_code'] = get_magic_quotes_gpc() ? stripslashes(trim($_POST['comment_code'])) : trim($_POST['comment_code']);
    if ($_POST['user_pass'] != '') $Mark_Config_Action['user_pass'] = MD5($_POST['user_pass']);

    if($_POST['runyear'] == '') {$Mark_Config_Action['runyear'] = date("Y");}else{$Mark_Config_Action['runyear'] = $_POST['runyear'];}
    if($_POST['runmon'] == '') {$Mark_Config_Action['runmon'] = date("d");}else{$Mark_Config_Action['runmon'] = $_POST['runmon'];}
    if($_POST['runday'] == '') {$Mark_Config_Action['runday'] = date("m");}else{$Mark_Config_Action['runday'] = $_POST['runday'];}
    if($_POST['runhour'] == '') {$Mark_Config_Action['runhour'] = date("H");}else{$Mark_Config_Action['runhour'] = $_POST['runhour'];}
    if($_POST['runmin'] == '') {$Mark_Config_Action['runmin'] = date("i");}else{$Mark_Config_Action['runmin'] = $_POST['runmin'];}
    if($_POST['runsec'] == '') {$Mark_Config_Action['runsec'] = date("s");}else{$Mark_Config_Action['runsec'] = $_POST['runsec'];}
    $Mark_Config_Action['site_name'] = str_replace('\\','',$Mark_Config_Action['site_name']);
   $Mark_Config_Action['copy_right'] = str_replace('\\','',$Mark_Config_Action['copy_right']);
    $code = "<?php\n\$Mark_Config_Action = " . var_export($Mark_Config_Action, true) . "\n?>";
    file_put_contents(FileLink.'/Index/Point/Index_Config_Action.php', $code);
    $display_info = "<script language=javascript>alert('操作成功！');window.location='".$Mark_Config_Action['site_link'].$Mark_Config_Action['level']."/".$Mark_Config_Action['root_link']."/Config.php'</script>";;
}
$site_name = $Mark_Config_Action['site_name'];
$site_desc = $Mark_Config_Action['site_desc'];
$site_link = $Mark_Config_Action['site_link'];
$site_key = $Mark_Config_Action['site_key'];
$user_nick = $Mark_Config_Action['user_nick'];
$user_name = $Mark_Config_Action['user_name'];
$copy_right = $Mark_Config_Action['copy_right'];
$root_link = $Mark_Config_Action['root_link'];
$site_mumber = $Mark_Config_Action['site_mumber'];
$style = $Mark_Config_Action['style'];
$nametwo = $Mark_Config_Action['nametwo'];
$fdlinks = $Mark_Config_Action['fdlinks'];
$write =  $Mark_Config_Action['write'];
$root_file = $Mark_Config_Action['root_file'];
$runyear = $Mark_Config_Action['runyear'];
$runmon = $Mark_Config_Action['runmon'];
$runday = $Mark_Config_Action['runday'];
$runhour = $Mark_Config_Action['runhour'];
$runmin = $Mark_Config_Action['runmin'];
$runsec = $Mark_Config_Action['runsec'];
$comment_code = isset($Mark_Config_Action['comment_code']) ? $Mark_Config_Action['comment_code'] : '';
?>
