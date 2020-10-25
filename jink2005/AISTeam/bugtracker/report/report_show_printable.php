<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: report_show_printable.php,v 1.6 2013/06/30 21:45:28 alex Exp $
 *
 */
session_start();
set_include_path("include".PATH_SEPARATOR."../include".PATH_SEPARATOR.".".PATH_SEPARATOR.get_include_path());
include("db.php");
include_once("group_function.php");
include("misc.php");
include("error.php");
include("string_function.php");
include("../include/user_function.php");
include("../include/status_function.php");
include("../include/project_function.php");
include("../include/customer_function.php");
include("../include/report_function.php");
include("../include/auth.php");

AuthCheckAndLogin();

if (!$_GET['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}
if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}
if (!$_GET['report_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "report_id");
}
$project_sql = "select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$line = $project_result->Recordcount();
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "project", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}
$project_name = $project_result->fields["project_name"];

$output = GetPrintableOutput($_GET['project_id'], $_GET['report_id'], "");

if ($output == "") {
	WriteSyslog("error", "syslog_not_found", "report", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "report");
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/style.css" rel="stylesheet" type="text/css">
<title><?php echo $project_name?></title>

</head>
<body>
<p>&nbsp;</p>
<?php
echo $output;
?>
<p>&nbsp;</p>
</body>
</html>
