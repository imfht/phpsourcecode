<?php
/**
 * 通用日志浏览器
 *  
 * @since 1.0 <2017-8-16> SoChishun <14507247@qq.com> Added.
 * 1. 目录树形层级结构显示，支持目录展开或收起
 * 2. 日志下载功能
 * 3. 日志删除功能
 * 4. 目录清空功能
 */
define('VERSION', '1.0.0');
$root = './java/logs/';
$path = isset($_REQUEST['path']) ? $_REQUEST['path'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

//debugmsg('logview.php[0]', 'debugtest');

if ($path && !file_exists($path)) {
    exit('路径不存在');
}
if ($action == "del" && $path) {
    try {
        if (is_dir($path)) {
            fn_rmdir($path);
        } else {
            unlink($path);
        }
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
    echo '删除成功!';
    header('location:logview.php?path=' . $root);
    exit;
}

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>LogAdmin - ',VERSION,'</title><style type="text/css">body{font-size:13px;}a{text-decoration:none}.aside{width:240px;max-height:600px;overflow:auto;float:left;font-size:13px}.main{width:800px;float:left;margin-left:10px;}li{list-style:none;margin-left:-40px;}</style></head><body><script src="//code.jquery.com/jquery-1.11.3.min.js"></script>';
echo '<a href="?path=', $root, '">/</a><br />';
if ($path) {
    echo $path;
    if (is_file($path)) {
        echo '[<a title="download [右键点击下载]" href="' . $path . '">&darr;</a>]';
    }
    echo '<br />';
}
echo '<div class="aside">';
echo '<ul>';
fn_scandir($path && is_dir($path) ? $path : $root);
echo '</ul>';
echo '</div>';

if ($path && is_file($path)) {
    echo '<div class="main">';
    echo '<textarea cols="150" rows="40">', htmlspecialchars(fn_tail($path)), '</textarea>';
    echo '</div>';
}
echo '<div style="clear:both"></div>';
echo '<script>$(".btn_switch").click(function () {var $this = $(this);if ($this.text() == "[-]") {$this.text("[+]").attr("title", "展开");$this.parent().children("ul").hide();} else {$this.text("[-]").attr("title", "收起");$this.parent().children("ul").show();}});</script>';
echo '</body></html>';

/**
 * 调试
 * @param type $location
 * @param type $msg
 */
function debugmsg($location, $msg) {
    $content = date('Y-m-d H:i:s') . PHP_EOL . $location . PHP_EOL . (is_string($msg) ? $msg : var_export($msg, true)) . PHP_EOL . PHP_EOL;
    $dir = './java/logs/';
    if (!is_dir($dir)) {
        mkdir($dir);
    }
    $path = $dir . 'log-' . date('mdh') . '.txt';
    fn_write($path, $content);
    //file_put_contents($path, $content, FILE_APPEND);
}

/**
 * PHP高效遍历文件夹（大量文件不会卡死）
 * @param string $path 目录路径
 * @param integer $level 目录深度
 */
function fn_scandir($path = './', $level = 0) {
    $file = new FilesystemIterator($path);
    $filename = '';
    $prefix = '';
    $url = '';
    foreach ($file as $fileinfo) {
        $filename = $fileinfo->getFilename();
        $filepath = $path . $filename;
        $prefix = $level > 0 ? ('|' . str_repeat('--', $level)) : '';
        if ($fileinfo->isDir()) {
            $filepath = $filepath . '/';
            $url = '<a title="[dir] ' . $filepath . '" href="?path=' . $filepath . '">' . $filename . '</a> [<a title="clear dir" href="?path=' . $filepath . '&action=del" onclick="return confirm(\'您确定要清空当前目录下所有文件吗?\')">x</a>]';
            echo '<li><strong>' . $prefix . $url . '/</strong> <a href="#" class="btn_switch" title="收起">[-]</a>' . PHP_EOL;
            echo '<ul>';
            fn_scandir($filepath, $level + 1);
            echo '</ul>';
            echo '</li>';
        } else {
            $url = '<a title="[file] ' . $filepath . '" href="?path=' . $filepath . '">' . $filename . '</a> [<a title="delete" href="?path=' . $filepath . '&action=del" onclick="return confirm(\'您确定要删除吗?\')">x</a>][<a title="download [右键点击下载]" href="' . $filepath . '">&darr;</a>]';
            echo '<li>' . $prefix . $url . '</li>' . PHP_EOL;
        }
        /*
          if ($fileinfo->isDir()) {
          fn_scandir($filepath, $level + 1);
          } */
    }
}

/**
 * 删除非空目录里面所有文件和子目录
 * @param string $dir
 * @return boolean
 */
function fn_rmdir($dir) {
    //先删除目录下的文件：
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;
            if (is_dir($fullpath)) {
                fn_rmdir($fullpath);
            } else {
                unlink($fullpath);
            }
        }
    }
    closedir($dh);
    //删除当前文件夹：
    if (rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}

/**
 * PHP高效读取文件
 * @param string $filepath
 * @return string
 */
function fn_tail($filepath) {
    if (file_exists($filepath)) {
        $fp = fopen($filepath, "r");
        $str = "";
        $buffer = 1024; //每次读取 1024 字节
        while (!feof($fp)) {//循环读取，直至读取完整个文件
            $str .= fread($fp, $buffer);
        }
        return $str;
    }
}

/**
 * PHP高效写入文件（支持并发）
 * @param string $filepath
 * @param string $content
 */
function fn_write($filepath, $content) {
    if ($fp = fopen($filepath, 'a')) {
        $startTime = microtime();
        // 对文件进行加锁时，设置一个超时时间为1ms，如果这里时间内没有获得锁，就反复获得，直接获得到对文件操作权为止，当然。如果超时限制已到，就必需马上退出，让出锁让其它进程来进行操作。
        do {
            $canWrite = flock($fp, LOCK_EX);
            if (!$canWrite) {
                usleep(round(rand(0, 100) * 1000));
            }
        } while ((!$canWrite) && ((microtime() - $startTime) < 1000));
        if ($canWrite) {
            fwrite($fp, $content);
        }
        fclose($fp);
    }
}
