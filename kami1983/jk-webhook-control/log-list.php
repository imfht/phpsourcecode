<?php
require_once 'lib.include.php';

/**
 * 用于显示日志列表的页面，比较简陋凑合用。
 */

$directory_tree=CWebhookLog::LogDirectoryTree();

echo '<h1>Log list</h1>';
foreach($directory_tree as $full_name){
    $filename=basename($full_name);
    if($_GET['remove'] == $filename && preg_match('/.*\.log/iU', $filename) ){
        //为了防止误删除只能删掉 *.log 的文件。
        unlink($full_name);
        header('Location:'.$_SERVER['PHP_SELF']);
        continue;
    }
    echo "<p><a href='logs/{$filename}'>{$filename}</a> <a href='log-list.php?remove=".urlencode($filename)."'>Remove it</a></p>";
}
