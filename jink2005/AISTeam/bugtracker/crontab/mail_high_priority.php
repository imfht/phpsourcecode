#!/usr/local/bin/php -q
<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: mail_high_priority.php,v 1.13 2010/07/27 09:24:17 alex Exp $
 *
 */
include("../include/db.php");
include("../include/misc.php");
include("../include/smtp.php");
include("../include/string.php");
include("../include/user_function.php");
include("../include/project_function.php");
include("../include/status_function.php");
include("../include/customer_function.php");
include("../include/report_function.php");

if (isset($_SERVER['SERVER_ADDR']) && ($_SERVER['REMOTE_ADDR'] != "127.0.0.1") && 
	($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR'])){
	echo "Only local access available.";
	exit;

}

$user_array = GetAllUsers(1,1);
$project_array = GetAllProjects();

$EmailLog = "Bug Tracker High Priority Reminder \n";
$EmailLog .= date("Y M j g:i A T")."\n";


for ($i = 0; $i< sizeof($project_array); $i++) {
	$project_id = $project_array[$i]->getprojectid();
	$project_name = $project_array[$i]->getprojectname();
	
	$sql = "select * from proj".$project_id."_report_table 
		where priority=5 and assign_to!=0 and assign_to!='-1' and 
		status in (select status_id from ".$GLOBALS['BR_status_table']."
		where status_type!='closed')";

	$result = $GLOBALS['connection']->Execute($sql) or die("Failed to get reports.");
	
	$additional_headers .= "X-Priority: 1\n";
	$headers .= "X-MSMail-Priority: High\n";
	
	while($row = $result->FetchRow()) {
		$report_id = $row["report_id"];
		$assign_to = $row["assign_to"];

		if (IsAccountDisabled($user_array, $assign_to)) {
			continue;
		}

		$user_email = UidToEmail($user_array, $assign_to);

		if (!IsEmailAddress($user_email)) {
			continue;
		}

		$EmailLog .= "[".$project_name."][id:".$report_id."] to ".UidToUsername($user_array, $assign_to)."\n";

		$subject="[".$project_name."][id:".$report_id."] auto reminder";

		//$subject = mb_encode_mimeheader($subject, "utf-8", "Q");
		$subject = "=?UTF-8?B?". base64_encode($subject)."?=";
	
		$message = '<html><body><style type="text/css">
			<!--
		body{
			line-height: 1.5;
			font-family: "Arial", "Helvetica", "sans-serif";
			font-size: 10pt;
			color: #262626;
		}
		TABLE, TD {
			font-family: "Arial", "Helvetica", "sans-serif";
			font-size: 10pt;
			line-height: 1.5;
		}
		TD.title {
			background-color:#EEEEEE;
			color:#5276B5;
		}
		td.prompt {
			background-color:#F5F5F5;
			vertical-align: top;
			color:#000066;
		}
        //  --></style>';
		
		$message .= GetReportOutput($project_id, $report_id, "email");
		$message .= '<p align="center">
		<a href="http://';
		if ($_SERVER["HOST"] == "") {
			$message .= $_SERVER['HTTP_HOST'];
		} else {
			$message .= $_SERVER["HOST"];
		}
		$message .= $GLOBALS["SYS_URL_ROOT"].'/index.php?project_id='.$project_id.'&report_id='.$report_id.'">
		Login to see this report</a></p>';
		$message .= "</body></html>";

		if ($SYSTEM["mail_function"] == "smtp") {
			$smtp = new smtp($SYSTEM["mail_smtp_server"], $SYSTEM["mail_smtp_port"], 
							 ($SYSTEM["mail_smtp_auth"] == "t")?TRUE:FALSE, 
							 $SYSTEM["mail_smtp_user"], $SYSTEM["mail_smtp_password"]);

			$smtp->debug = $GLOBALS["smtp_debug"];
			$smtp->sendmail($user_email, $SYSTEM['mail_from_name']."<".$SYSTEM['mail_from_email'].">", $subject, $message, "HTML", "", "", $additional_headers);

		} else {
			if (!function_exists('mail')) {
				return -1;
			}
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=utf-8\n";
			$headers .= "From: ".$SYSTEM['mail_from_name']."<".$SYSTEM['mail_from_email'].">\n";
			$headers .= $additional_headers;
			mail($user_email, $subject, $message, $headers);
		}
	}
}
echo $EmailLog;
?>
