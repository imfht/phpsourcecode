<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: member_qq.php 34239 2013-11-21 08:31:14Z nemohou $
 */
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
require_once DISCUZ_ROOT . './source/plugin/qq/config/config.php';

define('NOROBOT', TRUE);

if (!$_G['cache']['plugin']['qq']['isopen']) {
    showmessage('qq:plugin_not_open');
}

if ($_GET['action'] == 'login') { // debug 已有账号，绑定我的账号走此分支
    $ctl_obj = new logging_ctl();
    $_G['setting']['seccodestatus'] = 0;

    $ctl_obj->connect_guest = $connect_guest;

    $ctl_obj->extrafile = libfile('member/qq_logging', 'module');
    $ctl_obj->template = 'member/login';
    $ctl_obj->on_login();
} else { // debug 完善我的资料，即添加个新的论坛账号走此分支
    $_G['qc']['connect_auth_hash'] = $_GET['con_auth_hash'];
    if (!$_G['qc']['connect_auth_hash']) {
        $_G['qc']['connect_auth_hash'] = $_G['cookie']['con_auth_hash'];
    }

    $conopenid = authcode($_G['qc']['connect_auth_hash']);

    $ctl_obj = new register_ctl();
    $ctl_obj->setting = $_G['setting'];

    if ($_G['setting']['regconnect']) {
        $ctl_obj->setting['regstatus'] = $ctl_obj->setting['regstatus'] ? $ctl_obj->setting['regstatus'] : 1;
    }

    $_G['setting']['regclosed'] = $_G['setting']['regconnect'] && !$_G['setting']['regstatus'];

    loadcache('connect_blacklist');
    if (in_array($conopenid, $_G['cache']['connect_blacklist'])) {
        showmessage('qq:connect_uin_in_blacklist', $referer, array('changeqqurl' => $_G['connect']['discuz_change_qq_url']));
    }

    $_G['qc']['uinlimit'] = $_G['cache']['plugin']['qq']['register_uinlimit'] && C::t('#qq#qq_member_bindlog')->count_uid_openid_type($conopenid, '1') >= $_G['cache']['plugin']['qq']['register_uinlimit'];

    if ($_G['qc']['uinlimit']) {
        $_G['setting']['regconnect'] = false;
    }

    if (!$_G['setting']['regconnect']) {
        $ctl_obj->showregisterform = 0;
        $ctl_obj->setting['sitemessage']['register'] = array('a');
    }

    if ($_G['qc']['uinlimit']) {
        $ctl_obj->showregisterform = 0;
        $ctl_obj->setting['sitemessage']['register'] = array();
        $ctl_obj->setting['regconnect'] = false;
    }

    if ($_G['cache']['plugin']['qq']['register_regverify']) {
        $ctl_obj->setting['regverify'] = 0;
    }

    $_G['setting']['seccodestatus'] = 0;
    $_G['setting']['secqaa']['status'] = 0;

    $ctl_obj->setting['sendregisterverify'] = false;

	$ctl_obj->connect_guest = $connect_guest;


    loadcache(array('fields_connect_register', 'profilesetting'));
    foreach ($_G['cache']['fields_connect_register'] as $field => $data) {
        unset($_G['cache']['fields_register'][$field]);
    }
    $_G['cache']['profilesetting']['gender']['unchangeable'] = 0;
    $_G['cache']['profilesetting']['birthyear']['unchangeable'] = 0;
    $_G['cache']['profilesetting']['birthmonth']['unchangeable'] = 0;
    $_G['cache']['profilesetting']['birthday']['unchangeable'] = 0;
    $_G['cache']['fields_register'] = array_merge($_G['cache']['fields_connect_register'], $_G['cache']['fields_register']);

    if ($_G['cache']['plugin']['qq']['register_invite']) {
        $ctl_obj->setting['regstatus'] = 1;
    }


    if (!$_G['cache']['plugin']['qq']['register_regverify']) {
        $ctl_obj->setting['seccodestatus'] = $_G['setting']['seccodestatus'];
        $ctl_obj->setting['secqaa']['status'] = $_G['setting']['secqaa']['status'];
    }

    $ctl_obj->setting['ignorepassword'] = 1;
    $ctl_obj->setting['checkuinlimit'] = 1;
    $ctl_obj->setting['strongpw'] = 0;
    $ctl_obj->setting['pwlength'] = 0;

    if ($_GET['ac'] == 'bind') {
        $ctl_obj->setting['reglinkname'] = lang('plugin/qq', 'connect_register_bind');
    } else {
        $ctl_obj->setting['reglinkname'] = lang('plugin/qq', 'connect_register_profile');
    }

    $ctl_obj->extrafile = libfile('member/qq_register', 'module');

    $ctl_obj->template = 'member/register';

    $ctl_obj->on_register();
}
?>