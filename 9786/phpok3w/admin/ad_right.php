<?php
require_once("chk.php");  
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
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理系统</title>
<link rel="stylesheet" type="text/css" href="images/Style.css">
<style type="text/css">
<!--
.STYLE1 {
	color: #0000FF;
	font-weight: bold;
	font-size: 14px;
}
.STYLE2 {
	color: #FF0000;
	font-size: 14px;
} 
-->
</style>
</head>

<body>
<?php require_once("top.php") ?>
<table cellspacing="0" cellpadding="0" width="98%" align="center" border="0">
  <tbody>
    <tr>
      <td style="PADDING-LEFT: 2px; HEIGHT: 22px" background="images/tab_top_bg.gif">
          <table cellspacing="0" cellpadding="0" width="477" border="0">
        <tbody>
          <tr>
            <td>
			<table height="22" cellspacing="0" cellpadding="0" border="0">
              <tbody>
                <tr>
                  <td width="3"><img id="tabImgLeft__0" height="22" src="images/tab_active_left.gif" width="3" /></td>
                  <td  background="images/tab_active_bg.gif" class="tab">
                      <strong class="mtitle">网站管理后台 &gt;&gt; 管理首页 </strong></td>
                  <td width="3">
                      <img id="tabImgRight__0" height="22"  src="images/tab_active_right.gif"  width="3" /></td>
                </tr>
              </tbody>
            </table>
			</td>
          </tr>
        </tbody>
      </table></td>
    </tr>
    <tr>
      <td bgcolor="#ffffff">
	  <table  width="100%" border="0">
        <tbody>
          <tr>
            <td width="1" background="images/tab_bg.gif"><img height="1"   src="images/tab_bg.gif" width="1" /></td>
            <td width="100%"  valign="top"  style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px">
			<div id="tabContent__0" style="DISPLAY: block; VISIBILITY: visible">
              <table cellspacing="1" cellpadding="1" width="100%" align="center" bgcolor="#8ccebd" border="0">
                <tbody>
                  <tr>
                    <td  style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px"
                valign="top" bgcolor="#fffcf7">
                      <br />
<fieldset style="padding:10px;">
<legend>服务器信息</legend>
<br />

<table border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
  <tr>
    <td height="25" bgcolor="#FFFFFF">
         服务器类型：<?php $os = explode(" ", php_uname()); echo $os[0];?><br />
		内核版本： <?php if('/'==DIRECTORY_SEPARATOR){echo $os[2];}else{echo $os[1];} ?>
        (IP:<?php echo @get_current_user();?> - <?php echo $_SERVER['SERVER_NAME'];?>(<?php echo $_SERVER['SERVER_ADDR'];?>)
	 
		</td>
	 <td height="25" bgcolor="#FFFFFF">&nbsp;脚本解释引擎：
        <?php echo $_SERVER['SERVER_SOFTWARE'];?>
	 </td>
  </tr>
  <tr>
    <td height="25" bgcolor="#FFFFFF">
        &nbsp;站点物理路径：<?php echo $_SERVER['DOCUMENT_ROOT']?str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']):str_replace('\\','/',dirname(__FILE__));?></td>
    <td height="25" bgcolor="#FFFFFF">
        &nbsp;服务器主机名：<?php if('/'==DIRECTORY_SEPARATOR ){echo $os[1];}else{echo $os[2];} ?></td>
  </tr>
  <tr>
    <td height="25" bgcolor="#FFFFFF">
        &nbsp;PHP版本(php_version)：<?php echo PHP_VERSION;?></td>
    <td height="25" bgcolor="#FFFFFF">
        &nbsp;PHP运行方式：<?php echo strtoupper(php_sapi_name());?></td>
  </tr> 
  <tr>
        <td  height="25" bgcolor="#FFFFFF">你的IP地址是：<?php echo @$_SERVER['REMOTE_ADDR'];?>)</td>
        <td height="25" bgcolor="#FFFFFF">脚本占用最大内存（memory_limit）：  <?php echo show("memory_limit");?></td>
    </tr>
    <tr>
        <td  height="25" bgcolor="#FFFFFF">PHP安全模式（safe_mode）： <?php echo show("safe_mode");?></td>
        <td height="25" bgcolor="#FFFFFF" >POST方法提交最大限制（post_max_size）： <?php echo show("post_max_size");?></td>
    </tr>
    <tr>
        <td  height="25" bgcolor="#FFFFFF">上传文件最大限制（upload_max_filesize）： <?php echo show("upload_max_filesize");?></td>
        <td height="25" bgcolor="#FFFFFF">浮点型数据显示的有效位数（precision）： <?php echo show("precision");?></td>
    </tr>
    <tr>
        <td height="25" bgcolor="#FFFFFF">脚本超时时间（max_execution_time）： <?php echo show("max_execution_time");?>秒</td>
        <td height="25" bgcolor="#FFFFFF">socket超时时间（default_socket_timeout）： <?php echo show("default_socket_timeout");?>秒</td>
    </tr>
    <tr>
        <td height="25" bgcolor="#FFFFFF">PHP页面根目录（doc_root）： <?php echo show("doc_root");?></td>
        <td height="25" bgcolor="#FFFFFF">用户根目录（user_dir）： <?php echo show("user_dir");?></td>
    </tr>
    <tr>
        <td height="25" bgcolor="#FFFFFF">dl()函数（enable_dl）： <?php echo show("enable_dl");?></td>
        <td height="25" bgcolor="#FFFFFF">指定包含文件目录（include_path）： <?php echo show("include_path");?></td>
    </tr>
    <tr>
        <td height="25" bgcolor="#FFFFFF">显示错误信息（display_errors）： <?php echo show("display_errors");?></td>
        <td height="25" bgcolor="#FFFFFF">自定义全局变量（register_globals）： <?php echo show("register_globals");?></td>
    </tr>
    <tr>
        <td height="25" bgcolor="#FFFFFF">数据反斜杠转义（magic_quotes_gpc）： <?php echo show("magic_quotes_gpc");?></td>
        <td height="25" bgcolor="#FFFFFF">"&lt;?...?&gt;"短标签（short_open_tag）： <?php echo show("short_open_tag");?></td>
    </tr>
    <tr>
        <td height="25" bgcolor="#FFFFFF">"&lt;% %&gt;"ASP风格标记（asp_tags）： <?php echo show("asp_tags");?></td>
        <td height="25" bgcolor="#FFFFFF">忽略重复错误信息（ignore_repeated_errors）： <?php echo show("ignore_repeated_errors");?></td>
    </tr>
    <tr>
        <td height="25" bgcolor="#FFFFFF">忽略重复的错误源（ignore_repeated_source）： <?php echo show("ignore_repeated_source");?></td>
        <td height="25" bgcolor="#FFFFFF">报告内存泄漏（report_memleaks）： <?php echo show("report_memleaks");?></td>
    </tr>
    <tr>
        <td height="25" bgcolor="#FFFFFF">自动字符串转义（magic_quotes_gpc）： <?php echo show("magic_quotes_gpc");?></td>
        <td height="25" bgcolor="#FFFFFF">外部字符串自动转义（magic_quotes_runtime）： <?php echo show("magic_quotes_runtime");?></td>
    </tr>
    <tr>
        <td height="25" bgcolor="#FFFFFF">打开远程文件（allow_url_fopen）： <?php echo show("allow_url_fopen");?></td>
        <td height="25" bgcolor="#FFFFFF">声明argv和argc变量（register_argc_argv）： <?php echo show("register_argc_argv");?></td>
    </tr>
    <tr>
        <td height="25" bgcolor="#FFFFFF">Cookie 支持： <?php echo isset($_COOKIE)?'<font color="green">√</font>' : '<font color="red">×</font>';?></td>
        <td height="25" bgcolor="#FFFFFF">拼写检查（ASpell Library）： <?php echo isfun("aspell_check_raw");?></td>
    </tr>


</table>
</fieldset>
<br />

				</td>
                  </tr>
                </tbody>
              </table>
            </div></td>
            <td width="1" background="images/tab_bg.gif"><img height="1"
            src="images/tab_bg.gif" width="1" /></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
    <tr>
      <td background="images/tab_bg.gif" bgcolor="#ffffff"><img height="1"
      src="images/tab_bg.gif" width="1" /></td>
    </tr>
  </tbody>
</table>
</body>
</html>