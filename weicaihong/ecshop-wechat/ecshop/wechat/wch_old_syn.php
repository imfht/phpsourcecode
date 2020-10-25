<?php
/**
 * wch_old_syn.php UTF8
 * 老版本会员同步
 * User: djks
 * Date: 15/8/13 14:47
 * Copyright: http://www.weicaihong.com
 */

// 表前缀 $prefix
$tb_users = $prefix.'users';
$filed = ' user_id,user_name,parent_id,wxid,wxch_bd ';


// 查询绑定用户
$ok_sql = "SELECT $filed FROM `$tb_users` WHERE `wxid` != ''  AND wxch_bd = 'ok'";
$sth = $pdo_db->prepare($ok_sql);
$sth->execute();
$user_ok = $sth->fetchAll(PDO::FETCH_ASSOC);

// 未绑定用户
$no_sql = "SELECT * FROM `$tb_users` WHERE `wxid` != ''";
$sth = $pdo_db->prepare($no_sql);
$sth->execute();
$user_no = $sth->fetchAll(PDO::FETCH_ASSOC);

// 匹配绑定用户
foreach($user_no as $k=>$v)
{
    foreach($user_ok as $kk=>$vv)
    {
        if($user_no[$k]['wxid'] == $user_ok[$kk]['wxid'])
        {
            $user_no[$k] = $user_ok[$kk];
        }
    }

}
// 用户数据
$data['user'] = $user_no;

// 二维码数据
$qr_sql = "SELECT type,action_name,scene_id,ticket,scene,qr_path,affiliate,subscribe,scan FROM `wxch_qr` ";
$sth = $pdo_db->prepare($qr_sql);
$sth->execute();
$qr = $sth->fetchAll(PDO::FETCH_ASSOC);
$data['qr'] = $qr;

// 输出json
require_once('wch_json.php');