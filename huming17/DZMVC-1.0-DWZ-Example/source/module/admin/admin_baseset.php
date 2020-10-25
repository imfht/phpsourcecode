<?php

/**
 * 基本设置
 * @author HumingXu E-mail:huming17@126.com
 */
switch ($do) {
    case "update":
        $sitename = gpc('sitename', 'GP', $_G['sitename']);
        $sitedomain = gpc('sitedomain', 'GP', $_G['sitedomain']);
        $site_icp = gpc('site_icp', 'GP', $_G['site_icp']);
        $seokeywords = gpc('seokeywords', 'GP', $_G['seokeywords']);
        $seodescription = gpc('seodescription', 'GP', $_G['seodescription']);
        $site_rewrite = gpc('site_rewrite', 'GP', $_G['site_rewrite']);
        $site_stat = gpc('site_stat', 'GP', '');
        $tpldir = gpc('tpldir', 'GP', $_G['tpldir']);
        $aliyun_oss = gpc('aliyun_oss', 'GP','');
        $server_mail = gpc('server_mail', 'GP','');
        $server_upload = gpc('server_upload', 'GP','');

        if (!empty($sitename)) {
            DB::update('common_setting', array('svalue' => $sitename), "skey='sitename'");
        }
        if (!empty($sitedomain)) {
            DB::update('common_setting', array('svalue' => $sitedomain), "skey='sitedomain'");
        }
        if (!empty($seodescription)) {
            DB::update('common_setting', array('svalue' => $seodescription), "skey='seodescription'");
        }
        if (!empty($seokeywords)) {
            DB::update('common_setting', array('svalue' => $seokeywords), "skey='seokeywords'");
        }
        if (!empty($site_icp)) {
            DB::update('common_setting', array('svalue' => $site_icp), "skey='site_icp'");
        }
        if ($site_rewrite != '') {
            DB::insert('common_setting', array('skey' => 'site_rewrite', 'svalue' => $site_rewrite), false, true);
        }
        DB::insert('common_setting', array('skey' => 'site_stat', 'svalue' => $site_stat), false, true);
        if (!empty($tpldir)) {
            DB::insert('common_setting', array('skey' => 'tpldir', 'svalue' => $tpldir), false, true);
        }
        if ($aliyun_oss != '') {
            DB::insert('common_setting', array('skey' => 'aliyun_oss', 'svalue' => $aliyun_oss), false, true);
        }
        if (!empty($server_mail)) {
        	$server_mail = json_encode($server_mail);
            DB::insert('common_setting', array('skey' => 'server_mail', 'svalue' => $server_mail), false, true);
        }
        if (!empty($server_upload)) {
        	$server_upload = json_encode($server_upload);
            DB::insert('common_setting', array('skey' => 'server_upload', 'svalue' => $server_upload), false, true);
        }
        updatecache(array('setting'));
        echo '{
            "statusCode":"200",
            "message":"操作成功",
            "navTabId":"",
            "rel":"",
            "callbackType":"",
            "forwardUrl":"",
            "confirmMsg":""
        }';
        break;
    default:
        $sitename = $_G['setting']['sitename'];
        $sitedomain = $_G['setting']['sitedomain'];
        $seokeywords = $_G['setting']['seokeywords'];
        $seodescription = $_G['setting']['seodescription'];
        $site_icp = $_G['setting']['site_icp'];
        //获取模版目录模版
        $tpldir_array = get_templates();
        include template('admin/baseset/baseset');
}
?>