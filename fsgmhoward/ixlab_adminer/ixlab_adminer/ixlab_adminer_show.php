<?php
if (!defined('SYSTEM_ROOT') || ROLE != 'admin') {
    die('Insufficient Permissions');
}

if (isset($_GET['go'])) {
    // 检查SESSION，如果没激活则激活SESSION
    if (version_compare(phpversion(), '5.4.0', '<')) {
        // 快升级你的PHP！
        if (session_id() == '') {
            session_start();
        }
    } elseif (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // 保存一个已经认证的SESSION，加入这个isset则通过认证
    $_SESSION['ixnet_adminer_auth'] = true;
    header("Location:./plugins/ixlab_adminer/access.php");
    exit;
}

require_once 'ixnet_helpers.php';
$returnValue = require 'ixlab_adminer_desc.php';
$version = ixnet_helpers_version('ixlab_adminer.plugin.fsgmhoward.php', $returnValue['plugin']['version']);
loadhead();

// Load template
$html = file_get_contents(__DIR__.'/ixlab_adminer_show.template.html');
$html = str_ireplace('[[ CURRENT_BRANCH ]]', $version['branch'], $html);
$html = str_ireplace('[[ CURRENT_VERSION ]]', $version['currentVersion'], $html);
if($version['isUpToDate']) {
    $html = preg_replace('/\[\[ IF NOT_UP_TO_DATE \]\].*\[\[ FI NOT_UP_TO_DATE \]\]/s', '', $html);
} else {
    $html = str_ireplace('[[ LATEST_VERSION ]]', $version['remoteVersion'], $html);
    $html = str_ireplace('[[ LATEST_VERSION_LOG ]]', $version['raw']['updates'][$version['remoteVersion']], $html);
    $html = preg_replace('/\[\[ IF UP_TO_DATE \]\].*\[\[ FI UP_TO_DATE \]\]/s', '', $html);
}
// Remove all remaining brackets
$html = preg_replace('/\[\[.*?\]\]/', '', $html);
echo $html;
loadfoot();
