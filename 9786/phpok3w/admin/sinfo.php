<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-7-21
 * Time: 下午2:33
 */
error_reporting(E_ALL); //抑制所有错误信息
@header("content-Type: text/html; charset=utf-8"); //语言强制
ob_start();
define('HTTP_HOST', preg_replace('~^www\.~i', '', $_SERVER['HTTP_HOST']));
function show($varName){
    switch($result = get_cfg_var($varName)){
        case 0:
            return '<font color="red">×</font>';
            break;
        case 1:
            return '<font color="green">√</font>';
            break;
        default:
            return $result;
            break;
    }
}
// 检测函数支持
function isfun($funName = ''){
    if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) return '错误';
    return (false !== function_exists($funName)) ? '<font color="green">√</font>' : '<font color="red">×</font>';
}
$stime = date("Y-n-j H:i:s");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
</head>
 
</head>
<body>
<div id="page">
<div class="index_main">
    <h2 class="contentTitle">欢迎您!<?php echo $_SESSION['userName'];?>,祝您工作愉快!</h2>
</div>
<!--服务器相关参数-->
<table width="100%" cellpadding="3" cellspacing="0">
    <tr>
        <th colspan="4">服务器参数</th>
    </tr>
    <tr>
        <td>服务器当前时间</td>
        <td colspan="3"><?php echo $stime;?></td>
    </tr>
    <tr>
        <td>服务器域名/IP地址</td>
        <td colspan="3"><?php echo @get_current_user();?> - <?php echo $_SERVER['SERVER_NAME'];?>(<?php echo $_SERVER['SERVER_ADDR'];?>)&nbsp;&nbsp;你的IP地址是：<?php echo @$_SERVER['REMOTE_ADDR'];?></td>
    </tr>
    <tr>
        <td>服务器标识</td>
        <td colspan="3"><?php if($sysInfo['win_n'] != ''){echo $sysInfo['win_n'];}else{echo @php_uname();};?></td>
    </tr>
    <tr>
        <td width="13%">服务器操作系统</td>
        <td width="37%"><?php $os = explode(" ", php_uname()); echo $os[0];?>
            &nbsp;内核版本：
            <?php if('/'==DIRECTORY_SEPARATOR){echo $os[2];}else{echo $os[1];} ?></td>
        <td width="13%">服务器解译引擎</td>
        <td width="37%"><?php echo $_SERVER['SERVER_SOFTWARE'];?></td>
    </tr>
    <tr>
        <td>服务器语言</td>
        <td><?php echo getenv("HTTP_ACCEPT_LANGUAGE");?></td>
        <td>服务器端口</td>
        <td><?php echo $_SERVER['SERVER_PORT'];?></td>
    </tr>
    <tr>
        <td>服务器主机名</td>
        <td><?php if('/'==DIRECTORY_SEPARATOR ){echo $os[1];}else{echo $os[2];} ?></td>
        <td>绝对路径</td>
        <td><?php echo $_SERVER['DOCUMENT_ROOT']?str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']):str_replace('\\','/',dirname(__FILE__));?></td>
    </tr>
</table>

<table width="100%" cellpadding="3" cellspacing="0" align="center">
    <tr>
        <th colspan="4">PHP已编译模块检测</th>
    </tr>
    <tr>
        <td colspan="4"><span class="w_small">
        <?php
        $able=get_loaded_extensions();
        foreach ($able as $key=>$value) {
            if ($key!=0 && $key%13==0) {
                echo '<br />';
            }
            echo "$value&nbsp;&nbsp;";
        }
        ?>
        </span></td>
    </tr>
</table>
<table width="100%" cellpadding="3" cellspacing="0" align="center">
    <tr>
        <th colspan="4">PHP相关参数</th>
    </tr>
    <tr>
        <td>PHP版本（php_version）：</td>
        <td colspan="3"><?php echo PHP_VERSION;?></td>
    </tr>
    <tr>
        <td>PHP运行方式：</td>
        <td><?php echo strtoupper(php_sapi_name());?></td>
        <td>脚本占用最大内存（memory_limit）：</td>
        <td><?php echo show("memory_limit");?></td>
    </tr>
    <tr>
        <td>PHP安全模式（safe_mode）：</td>
        <td><?php echo show("safe_mode");?></td>
        <td>POST方法提交最大限制（post_max_size）：</td>
        <td><?php echo show("post_max_size");?></td>
    </tr>
    <tr>
        <td>上传文件最大限制（upload_max_filesize）：</td>
        <td><?php echo show("upload_max_filesize");?></td>
        <td>浮点型数据显示的有效位数（precision）：</td>
        <td><?php echo show("precision");?></td>
    </tr>
    <tr>
        <td>脚本超时时间（max_execution_time）：</td>
        <td><?php echo show("max_execution_time");?>秒</td>
        <td>socket超时时间（default_socket_timeout）：</td>
        <td><?php echo show("default_socket_timeout");?>秒</td>
    </tr>
    <tr>
        <td>PHP页面根目录（doc_root）：</td>
        <td><?php echo show("doc_root");?></td>
        <td>用户根目录（user_dir）：</td>
        <td><?php echo show("user_dir");?></td>
    </tr>
    <tr>
        <td>dl()函数（enable_dl）：</td>
        <td><?php echo show("enable_dl");?></td>
        <td>指定包含文件目录（include_path）：</td>
        <td><?php echo show("include_path");?></td>
    </tr>
    <tr>
        <td>显示错误信息（display_errors）：</td>
        <td><?php echo show("display_errors");?></td>
        <td>自定义全局变量（register_globals）：</td>
        <td><?php echo show("register_globals");?></td>
    </tr>
    <tr>
        <td>数据反斜杠转义（magic_quotes_gpc）：</td>
        <td><?php echo show("magic_quotes_gpc");?></td>
        <td>"&lt;?...?&gt;"短标签（short_open_tag）：</td>
        <td><?php echo show("short_open_tag");?></td>
    </tr>
    <tr>
        <td>"&lt;% %&gt;"ASP风格标记（asp_tags）：</td>
        <td><?php echo show("asp_tags");?></td>
        <td>忽略重复错误信息（ignore_repeated_errors）：</td>
        <td><?php echo show("ignore_repeated_errors");?></td>
    </tr>
    <tr>
        <td>忽略重复的错误源（ignore_repeated_source）：</td>
        <td><?php echo show("ignore_repeated_source");?></td>
        <td>报告内存泄漏（report_memleaks）：</td>
        <td><?php echo show("report_memleaks");?></td>
    </tr>
    <tr>
        <td>自动字符串转义（magic_quotes_gpc）：</td>
        <td><?php echo show("magic_quotes_gpc");?></td>
        <td>外部字符串自动转义（magic_quotes_runtime）：</td>
        <td><?php echo show("magic_quotes_runtime");?></td>
    </tr>
    <tr>
        <td>打开远程文件（allow_url_fopen）：</td>
        <td><?php echo show("allow_url_fopen");?></td>
        <td>声明argv和argc变量（register_argc_argv）：</td>
        <td><?php echo show("register_argc_argv");?></td>
    </tr>
    <tr>
        <td>Cookie 支持：</td>
        <td><?php echo isset($_COOKIE)?'<font color="green">√</font>' : '<font color="red">×</font>';?></td>
        <td>拼写检查（ASpell Library）：</td>
        <td><?php echo isfun("aspell_check_raw");?></td>
    </tr>
    <tr>
        <td>高精度数学运算（BCMath）：</td>
        <td><?php echo isfun("bcadd");?></td>
        <td>PREL相容语法（PCRE）：</td>
        <td><?php echo isfun("preg_match");?></td>
    <tr>
        <td>PDF文档支持：</td>
        <td><?php echo isfun("pdf_close");?></td>
        <td>SNMP网络管理协议：</td>
        <td><?php echo isfun("snmpget");?></td>
    </tr>
    <tr>
        <td>VMailMgr邮件处理：</td>
        <td><?php echo isfun("vm_adduser");?></td>
        <td>Curl支持：</td>
        <td><?php echo isfun("curl_init");?></td>
    </tr>
    <tr>
        <td>SMTP支持：</td>
        <td><?php echo get_cfg_var("SMTP")?'<font color="green">√</font>' : '<font color="red">×</font>';?></td>
        <td>SMTP地址：</td>
        <td><?php echo get_cfg_var("SMTP")?get_cfg_var("SMTP"):'<font color="red">×</font>';?></td>
    </tr>
</table>

<!--组件信息-->

<table width="100%" cellpadding="3" cellspacing="0" align="center">
    <tr>
        <th colspan="4" >组件支持</th>
    </tr>
    <tr>
        <td width="32%">FTP支持：</td>
        <td width="18%"><?php echo isfun("ftp_login");?></td>
        <td width="32%">XML解析支持：</td>
        <td width="18%"><?php echo isfun("xml_set_object");?></td>
    </tr>
    <tr>
        <td>Session支持：</td>
        <td><?php echo isfun("session_start");?></td>
        <td>Socket支持：</td>
        <td><?php echo isfun("socket_accept");?></td>
    </tr>
    <tr>
        <td>Calendar支持</td>
        <td><?php echo isfun('cal_days_in_month');?></td>
        <td>允许URL打开文件：</td>
        <td><?php echo show("allow_url_fopen");?></td>
    </tr>
    <tr>
        <td>GD库支持：</td>
        <td><?php
            if(function_exists(gd_info)) {
                $gd_info = @gd_info();
                echo $gd_info["GD Version"];
            }else{
                echo '<font color="red">×</font>';
            }?>
        </td>
        <td>压缩文件支持(Zlib)：</td>
        <td><?php echo isfun("gzclose");?></td>
    </tr>
    <tr>
        <td>IMAP电子邮件系统函数库：</td>
        <td><?php echo isfun("imap_close");?></td>
        <td>历法运算函数库：</td>
        <td><?php echo isfun("JDToGregorian");?></td>
    </tr>
    <tr>
        <td>正则表达式函数库：</td>
        <td><?php echo isfun("preg_match");?></td>
        <td>WDDX支持：</td>
        <td><?php echo isfun("wddx_add_vars");?></td>
    </tr>
    <tr>
        <td>Iconv编码转换：</td>
        <td><?php echo isfun("iconv");?></td>
        <td>mbstring：</td>
        <td><?php echo isfun("mb_eregi");?></td>
    </tr>
    <tr>
        <td>高精度数学运算：</td>
        <td><?php echo isfun("bcadd");?></td>
        <td>LDAP目录协议：</td>
        <td><?php echo isfun("ldap_close");?></td>
    </tr>
    <tr>
        <td>MCrypt加密处理：</td>
        <td><?php echo isfun("mcrypt_cbc");?></td>
        <td>哈稀计算：</td>
        <td><?php echo isfun("mhash_count");?></td>
    </tr>
</table>

<!--第三方组件信息-->
<table width="100%" cellpadding="3" cellspacing="0" align="center">
    <tr>
        <th colspan="4" >第三方组件</th>
    </tr>
    <tr>
        <td width="32%">Zend版本</td>
        <td width="18%"><?php $zend_version = zend_version();if(empty($zend_version)){echo '<font color=red>×</font>';}else{echo $zend_version;}?></td>
        <td width="32%">
            <?php
            $PHP_VERSION = PHP_VERSION;
            $PHP_VERSION = substr($PHP_VERSION,2,1);
            if($PHP_VERSION > 2){
                echo "ZendGuardLoader[启用]";
            }else{
                echo "Zend Optimizer";
            }
            ?>
        </td>
        <td width="18%"><?php if($PHP_VERSION > 2){echo (get_cfg_var("zend_loader.enable"))?'<font color=green>√</font>':'<font color=red>×</font>';} else{if(function_exists('zend_optimizer_version')){ echo zend_optimizer_version();}else{ echo (get_cfg_var("zend_optimizer.optimization_level")||get_cfg_var("zend_extension_manager.optimizer_ts")||get_cfg_var("zend.ze1_compatibility_mode")||get_cfg_var("zend_extension_ts"))?'<font color=green>√</font>':'<font color=red>×</font>';}}?></td>
    </tr>
</table>

<!--数据库支持-->

<table width="100%" cellpadding="3" cellspacing="0" align="center">
    <tr>
        <th colspan="4">数据库支持</th>
    </tr>
    <tr>
        <td width="32%">MySQL 数据库：</td>
        <td width="18%"><?php echo isfun("mysql_close");?>
            <?php
            if(function_exists("mysql_get_server_info")) {
                $s = @mysql_get_server_info();
                $s = $s ? '&nbsp; mysql_server 版本：'.$s : '';
                $c = '&nbsp; mysql_client 版本：'.@mysql_get_client_info();
                echo $s;
            }
            ?>
        </td>
        <td width="32%">ODBC 数据库：</td>
        <td width="18%"><?php echo isfun("odbc_close");?></td>
    </tr>
</table>
<a id="bottom"></a> </div>
</body>
</html>