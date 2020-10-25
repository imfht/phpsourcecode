<?php

error_reporting(0); //抑制所有错误信息
@header("content-Type: text/html; charset=utf-8"); //语言强制
ob_start();

function yesOrNo($condition)
{
    if ($condition) {
        echo '<font color="green">√</font>';
    } else {
        echo '<font class="warning" color="red">×</font>';
    }
}

//检测PHP设置参数
function show($varName)
{
    switch ($result = get_cfg_var($varName)) {
        case 0:
            return '<font class="warning" color="red">×</font>';
            break;

        case 1:
            return '<font color="green">√</font>';
            break;

        default:
            return $result;
            break;
    }
}

//检测PHP设置参数
function showText($varName)
{
    switch ($result = get_cfg_var($varName)) {
        case 0:
            echo '禁用';
            break;

        case 1:
            echo '开启';
            break;

        default:
            echo $result;
            break;
    }
}

//检测PHP设置参数
function showYesOrNo($varName, $rev = false)
{
    switch ($result = get_cfg_var($varName)) {
        case 0:
            echo !$rev ? '<font class="warning" color="red">×</font>' : '<font color="green">√</font>';
            break;

        case 1:
            echo !$rev ? '<font color="green">√</font>' : '<font class="warning" color="red">×</font>';
            break;

        default:
            echo $result;
            break;
    }
}

//检测PHP设置参数
function shwoBool($varName)
{
    switch ($result = get_cfg_var($varName)) {
        case 0:
            return false;
            break;
        default:
            return true;
            break;
    }
}

// 检测函数支持
function isfun($funName = '', $text = false)
{
    if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) return '错误';
    if ($text) {
        return (false !== function_exists($funName)) ? '开启' : '禁用';
    } else {
        return (false !== function_exists($funName)) ? '<font color="green">√</font>' : '<font class="warning" color="red">×</font>';
    }
}

function isfun1($funName = '')
{
    if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) return '错误';
    return (false !== function_exists($funName)) ? '正常' : '不可用';
}

//MySQL检测
if ($_POST['act'] == 'MySQL检测') {
    $host = isset($_POST['host']) ? trim($_POST['host']) : '';
    $port = isset($_POST['port']) ? (int)$_POST['port'] : '';
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $host = preg_match('~[^a-z0-9\-\.]+~i', $host) ? '' : $host;
    $port = intval($port) ? intval($port) : '';
    $login = preg_match('~[^a-z0-9\_\-]+~i', $login) ? '' : htmlspecialchars($login);
    $password = is_string($password) ? htmlspecialchars($password) : '';
} elseif ($_POST['act'] == '函数检测') {
    $funRe = "函数" . $_POST['funName'] . "支持状况检测结果：" . isfun1($_POST['funName']);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>naplesPHP 环境检测 </title>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style type="text/css">
        <!--
        * {
            font-family: Tahoma, "Microsoft Yahei", Arial;
        }

        body {
            text-align: center;
            margin: 0 auto;
            padding: 0;
            background-color: #FFFFFF;
            font-size: 12px;
            font-family: Tahoma, Arial
        }

        h1 {
            font-size: 26px;
            font-weight: normal;
            padding: 0;
            margin: 0;
            color: #444444;
        }

        h1 small {
            font-size: 11px;
            font-family: Tahoma;
            font-weight: bold;
        }

        a {
            color: #000000;
            text-decoration: none;
        }

        a.black {
            color: #000000;
            text-decoration: none;
        }

        b {
            color: #999999;
        }

        table {
            clear: both;
            padding: 0;
            margin: 0 0 10px;
            border-collapse: collapse;
            border-spacing: 0;
        }

        th {
            padding: 3px 6px;
            font-weight: bold;
            background: #3066a6;
            color: #FFFFFF;
            border: 1px solid #3066a6;
            text-align: left;
        }

        .th_1 {
            padding: 3px 6px;
            font-weight: bold;
            background: #666699;
            color: #FFFFFF;
            border: 1px solid #3066a6;
            text-align: left;
        }

        tr {
            padding: 0;
            background: #F7F7F7;
        }

        tr:hover {
            background: #e5e7eb;
        }

        td {
            padding: 3px 6px;
            border: 1px solid #CCCCCC;
        }

        input {
            padding: 2px;
            background: #FFFFFF;
            border-top: 1px solid #666666;
            border-left: 1px solid #666666;
            border-right: 1px solid #CCCCCC;
            border-bottom: 1px solid #CCCCCC;
            font-size: 12px
        }

        input.btn {
            width: 100%;
            font-weight: bold;
            height: 20px;
            line-height: 20px;
            padding: 0 6px;
            color: #666666;
            background: #f2f2f2;
            border: 1px solid #999;
            font-size: 12px
        }

        .bar {
            border: 1px solid #999999;
            background: #FFFFFF;
            height: 5px;
            font-size: 2px;
            width: 89%;
            margin: 2px 0 5px 0;
            padding: 1px;
            overflow: hidden;
        }

        .bar_1 {
            border: 1px dotted #999999;
            background: #FFFFFF;
            height: 5px;
            font-size: 2px;
            width: 89%;
            margin: 2px 0 5px 0;
            padding: 1px;
            overflow: hidden;
        }

        .barli_red {
            background: #ff6600;
            height: 5px;
            margin: 0px;
            padding: 0;
        }

        .barli_blue {
            background: #0099FF;
            height: 5px;
            margin: 0px;
            padding: 0;
        }

        .barli_green {
            background: #36b52a;
            height: 5px;
            margin: 0px;
            padding: 0;
        }

        .barli_1 {
            background: #999999;
            height: 5px;
            margin: 0px;
            padding: 0;
        }

        .barli {
            background: #36b52a;
            height: 5px;
            margin: 0px;
            padding: 0;
        }

        #page {
            width: 920px;
            padding: 0 20px;
            margin: 0 auto;
            text-align: left;
        }

        #header {
            position: relative;
            padding: 10px;
        }

        #footer {
            padding: 15px 15px;
            text-align: left;
            font-size: 12px;
            font-family: Tahoma, Verdana;
            line-height: 16px
        }

        #footer a {
            color: #0000FF
        }

        #lnmplink {
            position: absolute;
            top: 20px;
            left: 200px;
            text-align: right;
            font-weight: bold;
            color: #06C;
        }

        #lnmplink a {
            color: #0000FF;
            text-decoration: underline;
        }

        #lnmplink2 {
            position: absolute;
            top: 20px;
            right: 80px;
            text-align: right;
            font-weight: bold;
            color: #06C;
        }

        #lnmplink2 a {
            color: #0000FF;
            text-decoration: underline;
        }

        .w_small {
            font-family: Courier New;
        }

        .w_number {
            color: #f800fe;
        }

        .sudu {
            padding: 0;
            background: #5dafd1;
        }

        .suduk {
            margin: 0px;
            padding: 0;
        }

        .resNo {
            color: #FF0000;
        }

        .word {
            word-break: break-all;
        }

        -->
    </style>

</head>
<body>

<div id="page">
    <div id="header">
        <h1>naplesPHP 环境检测</h1>
        <div id="lnmplink2"><a title="项目地址" href="https://git.coding.net/yuri2/naples_php.git"
                               target="_blank">naplesPHP</a></div>
    </div>

    <!--服务器相关参数-->
    <table width="100%" cellpadding="3" cellspacing="0">
        <tr>
            <th colspan="4">服务器参数</th>
        </tr>
        <tr>
            <td>服务器域名/IP地址</td>
            <td colspan="3"><?php echo $_SERVER['SERVER_NAME']; ?>(<?php if ('/' == DIRECTORY_SEPARATOR) {
                    echo $_SERVER['SERVER_ADDR'];
                } else {
                    echo @gethostbyname($_SERVER['SERVER_NAME']);
                } ?>)
            </td>
        </tr>
        <tr>
            <td>服务器标识</td>
            <td colspan="3"><?php if ($sysInfo['win_n'] != '') {
                    echo $sysInfo['win_n'];
                } else {
                    echo @php_uname();
                }; ?></td>
        </tr>
        <tr>
            <td width="13%">服务器操作系统</td>
            <td width="37%"><?php $os = explode(" ", php_uname());
                echo $os[0]; ?> &nbsp;内核版本：<?php if ('/' == DIRECTORY_SEPARATOR) {
                    echo $os[2];
                } else {
                    echo $os[1];
                } ?></td>
            <td width="13%">服务器解译引擎</td>
            <td width="37%"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
        </tr>
        <tr>
            <td>服务器语言</td>
            <td><?php echo getenv("HTTP_ACCEPT_LANGUAGE"); ?></td>
            <td>服务器端口</td>
            <td><?php echo $_SERVER['SERVER_PORT']; ?></td>
        </tr>
        <tr>
            <td>服务器主机名</td>
            <td><?php if ('/' == DIRECTORY_SEPARATOR) {
                    echo $os[1];
                } else {
                    echo $os[2];
                } ?></td>
            <td>绝对路径</td>
            <td><?php echo $_SERVER['DOCUMENT_ROOT'] ? str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) : str_replace('\\', '/', dirname(__FILE__)); ?></td>
        </tr>
        <tr>
            <td>管理员邮箱</td>
            <td><?php echo $_SERVER['SERVER_ADMIN']; ?></td>
            <td>探针路径</td>
            <td><?php echo str_replace('\\', '/', __FILE__) ? str_replace('\\', '/', __FILE__) : $_SERVER['SCRIPT_FILENAME']; ?></td>
        </tr>
    </table>
    <table width="100%" cellpadding="3" cellspacing="0" align="center">
        <tr>
            <th colspan="8">PHP已编译模块检测</th>
        </tr>
        <?php
        $able = get_loaded_extensions();
        echo "<tr>";
        foreach ($able as $key => $value) {
            echo "<td style='width: 12.5%'>$value</td>";
            if (($key + 1) % 8 == 0) {
                echo "</tr><tr>";
            }
        }
        echo "</tr>";
        ?>
    </table>
    <table width="100%" cellpadding="3" cellspacing="0" align="center">
        <tr>
            <th colspan="3">运行环境</th>
        </tr>
        <tr>
            <td>PHP版本（php_version）</td>
            <td><?php echo PHP_VERSION; ?></td>
            <td><?php yesOrNo(version_compare(PHP_VERSION, '5.3.0', '>=')); ?></td>
        </tr>
        <tr>
            <td>PHP安全模式（safe_mode）</td>
            <td><?php showText('safe_mode'); ?></td>
            <td><?php showYesOrNo("safe_mode", true); ?></td>
        </tr>
        <tr>
            <td>Cookie 支持（safe_mode）</td>
            <td><?php echo isset($_COOKIE) ? '开启' : '禁用'; ?></td>
            <td><?php echo isset($_COOKIE) ? '<font color="green">√</font>' : '<font class="warning" color="red">×</font>'; ?></td>
        </tr>
        <tr>
            <td>自定义全局变量（register_globals）</td>
            <td><?php showText('register_globals'); ?></td>
            <td><?php showYesOrNo("register_globals", true); ?></td>
        </tr>
        <tr>
            <td>打开远程文件（allow_url_fopen）</td>
            <td><?php showText('allow_url_fopen'); ?></td>
            <td><?php showYesOrNo("allow_url_fopen"); ?></td>
        </tr>
        <tr>
            <td>文件读写权限</td>
            <?php
            $name = time();
            $file_path = __DIR__ . '/' . $name . '.tmp';
            file_put_contents($file_path, $name);
            $read_rel = file_get_contents($file_path);
            echo $read_rel == $name ? '<td>读写正常</td><td><font color="green">√</font></td>' : '<td>读写失败</td><td><font class="warning" color="red">×</font></td>';
            unlink($file_path);
            ?>
        </tr>
        <tr>
            <td>Curl支持</td>
            <td><?php echo isfun("curl_init", true); ?></td>
            <td><?php echo isfun("curl_init"); ?></td>
        </tr>
        <tr>
            <td width="32%">XML解析支持</td>
            <td width="18%"><?php echo isfun("xml_set_object", true); ?></td>
            <td width="18%"><?php echo isfun("xml_set_object"); ?></td>
        </tr>
        <tr>
            <td>Session支持</td>
            <td><?php echo isfun("session_start", true); ?></td>
            <td><?php echo isfun("session_start"); ?></td>
        </tr>
        <tr>
            <td>GD库支持</td>
            <td>
                <?php
                if (function_exists('gd_info')) {
                    $gd_info = @gd_info();
                    echo $gd_info["GD Version"];
                } else {
                    echo '禁用';
                }
                ?>
            </td>
            <td>
                <?php
                if (function_exists('gd_info')) {
                    echo '<font color="green">√</font>';
                } else {
                    echo '<font class="warning" color="red">×</font>';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>正则表达式函数库</td>
            <td><?php echo isfun("preg_match", true); ?></td>
            <td><?php echo isfun("preg_match"); ?></td>
        </tr>
        <tr>
            <td>Iconv编码转换</td>
            <td><?php echo isfun("iconv", true); ?></td>
            <td><?php echo isfun("iconv"); ?></td>
        </tr>
        <tr>
            <td>mbstring</td>
            <td><?php echo isfun("mb_eregi", true); ?></td>
            <td><?php echo isfun("mb_eregi"); ?></td>
        </tr>
        <tr>
            <td>MCrypt加密处理</td>
            <td><?php echo isfun("mcrypt_cbc", true); ?></td>
            <td><?php echo isfun("mcrypt_cbc"); ?></td>
        </tr>
        <tr>
            <td>哈稀计算</td>
            <td><?php echo isfun("mhash_count", true); ?></td>
            <td><?php echo isfun("mhash_count"); ?></td>
        </tr>
        <tr>
            <td>MySQL 数据库</td>
            <td><?php echo isfun("mysql_close", true); ?></td>
            <td><?php echo isfun("mysql_close"); ?></td>
        </tr>
        <tr>
            <td>PDO扩展</td>
            <td><?php echo (class_exists('pdo')) ? '开启' : '禁用'; ?></td>
            <td><?php echo (class_exists('pdo')) ? '<font color="green">√</font>' : '<font class="warning" color="red">×</font>'; ?></td>
        </tr>
    </table>

    <form action="<?php echo $_SERVER['PHP_SELF'] . "#bottom"; ?>" method="post">
        <input type="hidden" name="url" value="<?php echo $_SERVER['PHP_SELF']; ?>"/>


        <!--手动检测-->
        <table width="100%" cellpadding="3" cellspacing="0" align="center">
            <tr>
                <th colspan="2">手动检测</th>
            </tr>
            <tr>
                <td width="60%">
                    地址：<input type="text" name="host" value="localhost" size="10"/>&nbsp;&nbsp;&nbsp;&nbsp;
                    端口：<input type="text" name="port" value="3306" size="10"/>&nbsp;&nbsp;&nbsp;&nbsp;
                    用户名：<input type="text" name="login" size="10"/>&nbsp;&nbsp;&nbsp;&nbsp;
                    密码：<input type="password" name="password" size="10"/>
                </td>
                <td width="25%">
                    <input class="btn" type="submit" name="act" value="MySQL检测"/>
                </td>
            </tr>
            <?php
            if ($_POST['act'] == 'MySQL检测') {
                if (function_exists("mysql_close") == 1) {
                    $link = @mysql_connect($host . ":" . $port, $login, $password);
                    if ($link) {
                        echo "<script>alert('连接到MySql数据库正常')</script>";
                    } else {
                        echo "<script>alert('无法连接到MySql数据库！')</script>";
                    }
                } else {
                    echo "<script>alert('服务器不支持MySQL数据库！')</script>";
                }
                echo "<script>window.location.href='" . $_POST['url'] . "';</script>";
            }
            ?>
            <!--函数检测-->
            <tr>
                <td width="60%">
                    请输入您要检测的函数：
                    <input type="text" name="funName" size="50"/>
                </td>
                <td width="25%">
                    <input class="btn" type="submit" name="act" align="right" value="函数检测"/>
                </td>
            </tr>
            <?php
            if ($_POST['act'] == '函数检测') {
                $funName = $_POST['funName'];
                echo "<script>alert('$funRe')</script>";
                echo "<script>window.location.href='" . $_POST['url'] . "';</script>";

            }
            ?>
            <tr>
                <th colspan="2">环境确认</th>
            </tr>
            <tr>
                <td>确认以正常使用naplesPHP。如果环境检测有不通过的项，对于您的使用可能会有影响，建议修复所有不通过的检测项后再确认使用。如果确认后想重新见到该页面，请将 naples/install/lock
                    文件删除。
                </td>
                <td width="25%">
                    <input onclick="
                var wars=document.getElementsByClassName('warning');
                len=wars.length;
                if (len>0){
                    if (!confirm('发现'+len+'项环境检测未通过，确定要忽略吗？')){
                        return false;
                    }
                }"
                           class="btn" type="submit" name="act" align="right" value="环境确认"/>
                </td>
            </tr>
        </table>
        <?php
        if ($_POST['act'] == '环境确认') {
            file_put_contents(__DIR__ . '/lock', '');
            echo "<script>window.location.href='" . $_POST['url'] . "';</script>";
        }
        ?>
    </form>


    <div id="footer">
        <p>
            <span><a title="项目地址" href="https://git.coding.net/yuri2/naples_php.git" target="_blank">NaplesPHP</a>是一款轻（zhong）量级的PHP框架，致力于打造成一款顺手的php框架。NaplesPHP丰富的内容将会把你从重复的键盘敲击中解放出来！</span>
            <br/>
            <span>更多内容请参阅<a href="http://www.kancloud.cn/yuri2/naples" target="_blank">naplesPHP开发文档</a>。</span>
        </p>
        <p style="text-align: right;color: gray">本页部分代码引用自phpstudy探针</p>

    </div>

</div>
</body>
</html>