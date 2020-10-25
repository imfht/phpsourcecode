<?php
namespace workerbase\func;

/**
 * 脚本常用函数
 */

/**
 * 安装定时任务
 */
function installCrontab()
{
    global $crontab;

    $cronPath = '/etc/crontab';
    if (!is_readable($cronPath) || !is_writable($cronPath)) {
        echo "需要root权限.\n";
        return;
    }

    $cron = file_get_contents($cronPath);
    $is_first = true;//首次安卓定时任务配置
    $begin_str = '';
    $end_str = '';

    //如果已经存在历史配置则替换
    $beginPos = strpos($cron, '#workerbase_cron_begin');
    if (false !== $beginPos) {
        $is_first = false;
        $endPos = strpos($cron, '#workerbase_cron_end');
        $end_str = substr($cron, $endPos, strlen($cron)); //截取结束标记后的其他的字符串
        $cron = substr($cron, 0, $beginPos); //截取系统原内容，作为保留字符串内容，删掉自定义的历史配置
    }

    if ($is_first) {
        $begin_str = "\n";
        $end_str = "#workerbase_cron_end - 定时任务配置结束";
    }
    $cron .= $begin_str . "#workerbase_cron_begin - 由此开始作为定时任务配置\n{$crontab}\n" . $end_str;
    file_put_contents($cronPath, $cron);

    if (empty($crontab)) {
        echo "卸载定时任务成功.\n";
    } else {
        echo "安装定时任务成功.\n";
        echo "定时任务为:\n";
        echo $crontab ."\n";
    }

}