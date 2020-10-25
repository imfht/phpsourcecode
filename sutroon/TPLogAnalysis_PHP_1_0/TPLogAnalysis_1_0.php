<?php
/**
 * ThinkPHP 日志分析
 *  
 * @since 1.0 <2015-11-25> SoChishun <14507247@qq.com> Added.
 * @since 1.1 <2015-11-25> SoChishun 
 *      1.新增隐藏无效内容功能
 *      2.新增OTHER其他类型标签
 *      3.日记记录标签可点击并跳转到对应类型
 */
define('VERSION', '1.0.0');
define('LOG_PATH', '/Application/Runtime/Logs/');

$module = I('module');
$logfile = I('logfile');
$action = I('action');
$path = I('path');
switch ($action) {
    case 'download': // 下载
        if (!$path) {
            exit('路径有误!');
        }
        if (!file_exists($path)) {
            exit('路径不存在!');
        }
        $filename = basename($path);
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        readfile($path);
        exit(0);
        break;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>TPLogAnalysis <?php echo VERSION ?> - ThinkPHP日志分析器</title>
        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
        <style type="text/css">
            body {font-size:12px; color:#333;}
            a{text-decoration: none;}
            textarea{font-size:12px;line-height:18px; padding:5px;}
            th{font-weight: normal;}
            form {display:inline-block; margin-right:20px;}
            .userbar { margin-bottom: 10px;}
            .userbar a:before{content: '['}
            .userbar a:after{content: ']'}
            .blue{color:#0000DB}
            .lightblue{color:#1bd1a5}
            .purple{color:#9900ff}
            .green{color:#009900}
            .red{color:#F00}
            .grey {color:#999;}
            .title { color:#F00; font-weight: bold; padding:5px; border-bottom:dotted 1px #F60;}
            .log { border-bottom:dotted 1px #CCC; padding:3px;}
            .flags { margin:5px 0px 5px -40px;}
            .flags li { display:inline-block; list-style: none; padding:3px 10px; border-left:solid 1px #CCC;}
            .flags li:first-child { border:none;}
            .flags a, .flags a:active { color:#00C;}
            .flags li.active { color:#F00; font-weight:bold; background-color:#009500;}
            .flags li.active a { color:#FFF;}
            .flags a:hover { color:#F00;}
            .content { max-height: 600px; overflow-y: auto;}
            h3 { font-size: 16px;}
            h3, .file-opt { display:inline-block; margin-right:10px;}
            #chk-hidden { vertical-align: middle; margin-right:5px;}
        </style>
    </head>
    <body>
        <fieldset>
            <legend>选择项目：</legend>
            <?php
            // 选择项目模块
            echo '<form>';
            echo '项目：';
            echo '<select name="module" required="required">';
            echo '<option value="">==选择项目==</option>';
            $dirs = TPLogAnalysis::get_dir_contents(LOG_PATH);
            foreach ($dirs as $dir) {
                echo '<option value="' . $dir['name'] . '"' . ($module == $dir['name'] ? ' selected="selected"' : '') . '>' . $dir['name'] . '</option>';
            }
            echo '</select>';
            echo '<button type="submit">选择</button>';
            echo '</form>';
            // 选择日志文件
            if ($module) {
                echo '<form>';
                echo '日志文件：';
                $dirs = TPLogAnalysis::get_dir_contents(LOG_PATH . $module . '/', array('size' => true));
                echo '<select name="logfile" required="required">';
                echo '<option value="">==选择日志文件==</option>';
                $size = 0;
                foreach ($dirs as $dir) {
                    $size+=$dir['size'];
                    echo '<option value="' . $dir['name'] . '"' . ($logfile == $dir['name'] ? ' selected="selected"' : '') . '>' . $dir['name'] . ' (' . format_bytes($dir['size']) . ')' . '</option>';
                }
                echo '</select>';
                echo '<span style="color:#999">(', format_bytes($size), ')</span>';
                echo '<button type="submit">开始分析</button>';
                echo '<input type="hidden" name="module" value="' . $module . '" />';
                echo '</form>';
            }
            ?>

        </fieldset>
        <?php
        // 显示日志内容
        if ($module && $logfile) {
            $path = LOG_PATH . $module . '/' . $logfile;
            echo '<h3>' . $module . '/' . $logfile . '</h3>';
            echo '<div class="file-opt"><a href="?action=download&path=', urlencode(path_rtoa($path)), '">[下载]</a></div>';
            $flag = I('flag', 'ALL');
            // 日志级别 从上到下，由低到高
            $flags = array('ALL' => '所有', 'EMERG' => '严重错误: 导致系统崩溃无法使用', 'ALERT' => '警戒性错误: 必须被立即修改的错误', 'CRIT' => '临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样', 'ERR' => '一般错误: 一般性错误', 'WARN' => '警告性错误: 需要发出警告的错误', 'NOTIC' => '通知: 程序可以运行但是还不够完美的错误', 'INFO' => '信息: 程序输出信息', 'DEBUG' => '调试: 调试信息', 'SQL' => 'SQL：SQL语句 注意只在调试模式开启时有效', 'OTHER' => '其他：用户自定义级别，例如LOGIN,LOGOUT,OPERATE等');
            $sysflags = array('EMERG', 'ALERT', 'CRIT', 'ERR', 'WARN', 'NOTIC', 'INFO', 'DEBUG', 'SQL');
            // 日志类型导航
            echo '<div>日志类型：<ul class="flags">';
            foreach ($flags as $key => $text) {
                echo '<li', ($flag == $key ? ' class="active"' : ''), '><a href="?module=', $module, '&logfile=', $logfile, '&flag=', $key, '" title="', $text, '">', $key, '</a></li>';
            }
            if ($flag && !array_key_exists($flag, $flags)) {
                echo '<li class="active"><a href="?module=', $module, '&logfile=', $logfile, '&flag=', $flag, '" title="用户自定义级别:', $flag, '">', $flag, '</a></li>';
            }
            echo '</ul></div>';
            echo '<table cellpadding="0" cellspacing="0"><tr><td><input type="checkbox" id="chk-hidden" /></td><td>隐藏无效内容</td></tr></table>';
            $path = path_rtoa($path);
            if (!file_exists($path) || !is_readable($path)) {
                exit('文件不存在或不可读!');
            }
            echo '<div class="content">';
            echo '<div class="section">'; // section
            IF ($flag == 'ALL') {
                $flag = false;
            }
            $file = fopen($path, 'r');
            $i = 1;
            $n = 0;
            while (!feof($file)) {
                $sline = trim(fgetss($file, 2048));
                if (!$sline) {
                    continue;
                }
                $n++;
                if ('[' == $sline[0]) {
                    if ($n > 1) {
                        echo '</div><div class="section">';
                    }
                    echo '<div class="title">', $sline, '</div>';
                } else {
                    $type = substr($sline, 0, strpos($sline, ':'));
                    if ($flag) {
                        if ('OTHER' == $flag && in_array($type, $sysflags)) {
                            continue;
                        } else if ('OTHER' != $flag && $flag != $type) {
                            continue;
                        }
                    }
                    echo '<div class="log">', $i, '. <a href="?module=', $module, '&logfile=', $logfile, '&flag=', $type, '">', $type, '</a>', substr($sline, strlen($type)), '</div>';
                    $i++;
                }
            }
            fclose($file);
            echo '</div>'; // /section
            echo '</div>';
            echo '<div>共 ', $i - 1, ' 条记录!</div>';
        }
        ?>
        <script type="text/javascript">
            // 显示或隐藏无效记录 2015-11-25 SoChishun Added.
            $('#chk-hidden').change(function () {
                if ($(this).prop('checked')) {
                    $('.section').hide();
                    $('.log').parent().show();
                } else {
                    $('.section').show();
                }
            })
        </script>
    </body>
</html>
<?php

//=======================通用函数====================
/**
 * 获取浏览器参数
 * @param string $name
 * @param mixed $defv
 * @return mixed
 * @since 1.0 <2015-8-13> SoChishun Added.
 */
function I($name, $defv = '') {
    if (isset($_GET[$name])) {
        return $_GET[$name];
    }
    return isset($_POST[$name]) ? $_POST[$name] : $defv;
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 * @since 1.0 <2015-10-7> from ThinkPHP
 */
function redirect($url, $time = 0, $msg = '') {
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg))
        $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    } else {
        $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}

/**
 * 获取文件扩展名类型
 * @param string $exten 扩展名(不带.)
 * @return string
 * @since 1.0 <2015-10-9> SoChishun Added.
 */
function get_exten_catetory($exten) {
    if ($exten) {
        $filetypes = array('zip' => array('zip', 'rar', '7-zip', 'tar', 'gz', 'gzip'), 'doc' => array('txt', 'rtf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'wps', 'et'), 'script' => array('php', 'js', 'css', 'c'), 'image' => array('jpg', 'jpeg', 'png', 'gif', 'tiff', 'psd', 'bmp', 'ico'));
        foreach ($filetypes as $catetory => $extens) {
            if (in_array($exten, $extens)) {
                return $catetory;
            }
        }
    }
    return '';
}

/**
 * 绝对路径转相对路径
 * @param string $path
 * @return string
 * @since 1.0 <2015-10-9> SoChishun Added.
 */
function path_ator($path) {
    $root = $_SERVER['DOCUMENT_ROOT'];
    $path = substr($path, strlen($root));
    if ('/' != DIRECTORY_SEPARATOR) {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
    }
    return $path;
}

/**
 * 相对路径转绝对路径
 * @param string $path
 * @return string
 * @since 1.0 <2015-10-9> SoChishun Added.
 */
function path_rtoa($path) {
    $root = $_SERVER['DOCUMENT_ROOT'];
    if ('/' != DIRECTORY_SEPARATOR) {
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
    return $root . $path;
}

/**
 * 获取文件的目录地址
 * @param string $path
 * @param boolean $is_r 是否相对路径
 * @return string
 * @since 1.0 <2015-10-9> SoChishun Added.
 */
function path_getdir($path, $is_r = true) {
    if (!$path || is_dir($is_r ? path_rtoa($path) : $path)) {
        return $path;
    }
    return pathinfo($path, PATHINFO_DIRNAME);
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++)
        $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

class TPLogAnalysis {

    /**
     * 版本号
     * @var string
     * @since 1.0 <2015-11-25> SoChishun Added.
     */
    CONST VERSION = '1.0.0';

    /**
     * 返回指定路径下的内容
     * @param string $directory 路径
     * @param array $config 选项
     * @return array
     * @throws Exception
     * @since 1.0 <2015-5-11> SoChishun Added.
     * @since 1.1 <2015-10-8> SoChishun 新增filetype文件类别属性
     */
    static function get_dir_contents($directory, $options = array()) {
        $config = array('name' => true, 'path' => true, 'real_path' => true, 'relative_path' => false, 'exten' => false, 'ctime' => false, 'mtime' => false, 'size' => false, 'is_dir' => true, 'is_file' => false, 'is_link' => false, 'is_executable' => false, 'is_readable' => false, 'is_writable' => false, 'filetype' => false);
        if ($options) {
            $config = array_merge($config, $options);
        }
        try {
            $dir = new DirectoryIterator(path_rtoa($directory));
        } catch (Exception $e) {
            throw new Exception($directory . ' is not readable');
        }
        $files = array();
        foreach ($dir as $file) {
            if ($file->isDot()) {
                continue;
            }
            if ($config['name']) {
                $item['name'] = $file->getFileName();
            }
            if ($config['path']) {
                $item['path'] = $file->getPath();
            }
            if ($config['real_path']) {
                $item['real_path'] = $file->getRealPath();
            }
            if ($config['relative_path']) {
                $item['relative_path'] = path_ator($file->getRealPath());
            }
            $exten = $file->getExtension();
            if ($config['exten']) {
                $item['exten'] = $exten;
            }
            if ($config['filetype']) {
                $item['filetype'] = get_exten_catetory($exten);
            }
            if ($config['mtime']) {
                $item['mtime'] = $file->getMTime();
            }
            if ($config['ctime']) {
                $item['ctime'] = $file->getCTime();
            }
            if ($config['size']) {
                $item['size'] = $file->getSize();
            }
            if ($config['is_dir']) {
                $item['is_dir'] = $file->isDir();
            }
            if ($config['is_file']) {
                $item['is_file'] = $file->isFile();
            }
            if ($config['is_link']) {
                $item['is_link'] = $file->isLink();
            }
            if ($config['is_executable']) {
                $item['is_executable'] = $file->isExecutable();
            }
            if ($config['is_readable']) {
                $item['is_readable'] = $file->isReadable();
            }
            if ($config['is_writable']) {
                $item['is_writable'] = $file->isWritable();
            }
            $files[] = $item;
        }
        return $files;
    }

}
