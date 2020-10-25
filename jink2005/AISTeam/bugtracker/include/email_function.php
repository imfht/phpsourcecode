<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: email_function.php,v 1.31 2013/07/07 21:31:13 alex Exp $
 *
 */
/*
	Caller should include:
	include("project_function.php");
	include("customer_function.php");
	include("misc.php");
	include("report_function.php");
*/
include_once("smtp.php");
include_once("datetime_function.php");

function SendUpdateUserEamil($username, $password, $type)
{
	global $SYSTEM;

	if ($SYSTEM['mail_function'] == 'nosend') {
		return 0;
	}

	$sql = "select email,language from ".$GLOBALS['BR_user_table']." 
		where username=".$GLOBALS['connection']->QMagic($username)." and account_disabled!='t'";
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	if ($result->Recordcount() != 1) {		
		return -1;
	}

	$language = $result->fields["language"];
	$email = $result->fields["email"];
	if (!IsEmailAddress($email)) {
		return -1;
	}

	$STRING = GetStringArray($language, false);
	$message = '<html><body><style type="text/css">
		<!--
		body{
			line-height: 1.5;
			font-family: "Arial", "Helvetica", "sans-serif";
			font-size: 10pt;
			color: #262626;
		}
        //  --></style>';
	
	if ($type == "password") {
		$subject = str_replace('@program_name@', $SYSTEM['program_name'], $STRING['update_user_email_subject']);
		$message .= str_replace('@program_name@', $SYSTEM['program_name'], $STRING['update_user_email_text']);
	} else {
		$subject = str_replace('@program_name@', $SYSTEM['program_name'], $STRING['new_user_email_subject']);
		$message .= str_replace('@program_name@', $SYSTEM['program_name'], $STRING['new_user_email_text']);
	}
	$subject = "=?UTF-8?B?".base64_encode($subject)."?=";

	$message = str_replace('@username@', $username, $message);
	$message = str_replace('@password@', $password, $message);
	$message = str_replace('@url@', '<a href="http://'.$_SERVER['HTTP_HOST'].$GLOBALS["SYS_URL_ROOT"].'/index.php">
			http://'.$_SERVER['HTTP_HOST'].$GLOBALS["SYS_URL_ROOT"].'/index.php</a>', $message);
	$message = str_replace('@mail_from_name@', $SYSTEM['mail_from_name'], $message);
	$message .= '</body></html>';

	flush();
	$message = str_replace("\r\n", "\n", $message);
	$message = str_replace("\n", "\r\n", $message);
	if ($SYSTEM["mail_function"] == "smtp") {
		$smtp = new smtp($SYSTEM["mail_smtp_server"], $SYSTEM["mail_smtp_port"], 
						 ($SYSTEM["mail_smtp_auth"] == "t")?TRUE:FALSE, 
						 $SYSTEM["mail_smtp_user"], $SYSTEM["mail_smtp_password"]);

		$smtp->debug = $GLOBALS["smtp_debug"];
		$smtp->sendmail($email, "=?UTF-8?B?".base64_encode($SYSTEM['mail_from_name'])."?=<".$SYSTEM['mail_from_email'].">", $subject, $message, "HTML");
	} else {
		if (!function_exists('mail')) {
			return -1;
		}
		$headers  = "MIME-Version: 1.0 \r\n";
		$headers .= "Content-type: text/html; charset=utf-8 \r\n";
		$headers .= "From: =?UTF-8?B?".base64_encode($SYSTEM['mail_from_name'])."?=<".$SYSTEM['mail_from_email']."> \r\n";
		mail($email, $subject, $message, $headers);
	}
	
	return 0;
}

/*
 * The $extra_to_uid format is "1,3,4" (separated by ",")
 *
 */
function SendReportEmail($project_id, $report_id, $extra_to_uid = "")
{
	global $SYSTEM;

	if ($SYSTEM['mail_function'] == 'nosend') {
		return 0;
	}

	$cc_array = array();
	$to_array = array();

	// Get the project name
	$project_sql = "select project_name from ".$GLOBALS['BR_project_table']." where project_id=".$project_id;
	$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
	if ($project_result->Recordcount() != 1) {
		return -1;
	}
	$project_name = $project_result->fields["project_name"];

	// Get auto mail to(cc list) of the project
	if ($SYSTEM['allow_subscribe'] == 't') {
		$auto_email_sql = "select user_id from ".$GLOBALS['BR_proj_auto_mailto_table']." where project_id=".$project_id;
	} else {
		$auto_email_sql = "select user_id from ".$GLOBALS['BR_proj_auto_mailto_table']." where 
			project_id=".$project_id." and can_unsubscribe='f'";
	}
	$auto_email_result = $GLOBALS['connection']->Execute($auto_email_sql) or DBError(__FILE__.":".__LINE__);
	while ($row = $auto_email_result->FetchRow()) {
		$user_id = $row["user_id"];
		array_push($cc_array, $user_id);
	}

	// Get the date of this report
	$report_sql = "select status, summary, assign_to, area, minor_area, priority from proj".$project_id."_report_table where report_id=".$report_id;
	$report_result = $GLOBALS['connection']->Execute($report_sql) or DBError(__FILE__.":".__LINE__);
	if ($report_result->Recordcount() != 1) {
		return -1;
	}
	$summary = $report_result->fields["summary"];
	$status = $report_result->fields["status"];
	$priority = $report_result->fields["priority"];
	$assign_to = $report_result->fields["assign_to"];
	$area = $report_result->fields["area"];
	$minor_area = $report_result->fields["minor_area"];
	if ($assign_to != -1) {
		array_push($to_array, $assign_to);
	}

	// Get area owner and minor area owner
	if ($area != "") {
		$area_sql = "select area_id, owner from ".$GLOBALS['BR_proj_area_table']." where project_id='".$project_id."' and area_name=".$GLOBALS['connection']->QMagic($area)." and area_parent=0";
		$area_result = $GLOBALS['connection']->Execute($area_sql) or DBError(__FILE__.":".__LINE__);
		$line = $area_result->Recordcount();
		if ($line == 1) {
			$area_id = $area_result->fields["area_id"];
			$area_owner = $area_result->fields["owner"];
					
			if ($area_owner != -1) {
				array_push($to_array, $area_owner);
			}
			if (($minor_area != "") && ($area_id != "")) {
				$minor_area_sql = "select owner from ".$GLOBALS['BR_proj_area_table']." 
					where project_id='".$project_id."' and area_name=".$GLOBALS['connection']->QMagic($minor_area)." and area_parent=".$area_id;
				$minor_area_result = $GLOBALS['connection']->Execute($minor_area_sql) or 
					DBError(__FILE__.":".__LINE__);
				if ($minor_area_result->Recordcount() == 1) {
					$minor_area_owner = $minor_area_result->fields["owner"];
					if ($minor_area_owner != -1) {
						array_push($to_array, $minor_area_owner);
					}
				}
			}
		}
	}
	$extra_to = explode(",", $extra_to_uid);
	for ($i = 0; $i < sizeof($extra_to); $i++) {
		if ($extra_to[$i] != "") {
			array_push($to_array, $extra_to[$i]);
		}
	}
	$to_array = ArrayUnique($to_array);

	$userarray = GetAllUsers(1, 0);
	// Remove redundant users and put email address into $to_email
	$to_email_array = array();
	for ($i=0; $i<sizeof($to_array); $i++) {
		$email = UidToEmail($userarray, $to_array[$i]);
		if (IsEmailAddress($email)) {
			array_push($to_email_array, $email);
		}
	}
	$cc_email_array = array();
	for ($i = 0; $i < sizeof($cc_array); $i++) {
		$email = UidToEmail($userarray, $cc_array[$i]);
		if (IsEmailAddress($email) && (IsInArray($to_email_array, $email) == -1) ) {
			array_push($cc_email_array, $email);
		}
	}

	$status_array = GetStatusArray();

	$to = implode(",", $to_email_array);
	$cc = implode(",", $cc_email_array);

	$subject="[".$project_name."][id:".$report_id."][".GetStatusNameByID($status_array, $status)."] ".$summary;

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
		<a href="http://'.$_SERVER['HTTP_HOST'].$GLOBALS["SYS_URL_ROOT"].'/report/report_show.php?project_id='.$project_id.'&report_id='.$report_id.'">
		Login to see this report</a></p>';
	$message .= "</body></html>";

	$additional_headers = "";
	if ($priority == 5) {
		$additional_headers .= "X-Priority: 1\r\n";
		$additional_headers .= "X-MSMail-Priority: High\r\n";
	}

	flush();
	$message = str_replace("\r\n", "\n", $message);
	$message = str_replace("\n", "\r\n", $message);

	$email = $_SESSION[SESSION_PREFIX.'email'];
	if (IsEmailAddress($email)) {
		$from = "=?UTF-8?B?".base64_encode($SYSTEM['mail_from_name']." - ".$_SESSION[SESSION_PREFIX.'username'])."?=<".$email.">";
	} else {
		$from = "=?UTF-8?B?".base64_encode($SYSTEM['mail_from_name']." - ".$_SESSION[SESSION_PREFIX.'username'])."?=<".$SYSTEM['mail_from_email'].">";
	}

	if ($SYSTEM["mail_function"] == "smtp") {
		$smtp = new smtp($SYSTEM["mail_smtp_server"], $SYSTEM["mail_smtp_port"], 
						 ($SYSTEM["mail_smtp_auth"] == "t")?TRUE:FALSE, 
						 $SYSTEM["mail_smtp_user"], $SYSTEM["mail_smtp_password"]);

		$smtp->debug = $GLOBALS["smtp_debug"];
		$smtp->sendmail($to, $from, $subject, $message, "HTML", $cc, "", $additional_headers);

	} else {
		if (!function_exists('mail')) {
			return -1;
		}
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		// additional headers 
		$headers .= "From: ".$from."\r\n";
		if (strlen($cc) > 0) {
			$headers .= "Cc: ".$cc."\r\n";
		}
		$headers .= $additional_headers;
		mail($to, $subject, $message, $headers);
	}

	return 0;
}

/* Send email to faq assign_to and CC to project cc to.
 *
 * The $extra_cc_uid format is "1,3,4" (separated by ",")
 */
function SendFAQEmail($faq_id, $extra_cc_uid)
{
	global $SYSTEM;

	if ($SYSTEM['mail_function'] == 'nosend') {
		return 0;
	}

	$cc_array = array();

	if (!$faq_id) {
		return 0;
	}

	$STRING = GetStringArray($_SESSION[SESSION_PREFIX.'language'], false);

	// Get FAQ Content
	$sql = "select * from ".$GLOBALS['BR_faq_content_table']."  where faq_id='".$faq_id."'";
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	$line = $result->Recordcount();
	if ($line != 1) {
		return -1;
	}

	$project_id = $result->fields["project_id"];
	$question = $result->fields["question"];
	$answer = $result->fields["answer"];
	$is_verified = $result->fields["is_verified"];
	$assign_to = $result->fields["assign_to"];

	if (($is_verified == 't') || ($assign_to == -1) || (!$assign_to)) {
		// No need to send email when verified, or assign_to is null.
		return 0;
	}

	// Get the project name
	$project_sql = "select project_name from ".$GLOBALS['BR_project_table']." where project_id=".$project_id;
	$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
	if ($project_result->Recordcount() != 1) {
		return -1;
	}
	$project_name = $project_result->fields["project_name"];

	$extra_cc = explode(",", $extra_to_uid);
	for ($i = 0; $i < sizeof($extra_cc); $i++) {
		if ($extra_cc[$i] != "") {
			array_push($cc_array, $extra_cc[$i]);
		}
	}
	
	// Get auto mail to(cc list) of the project
	if ($SYSTEM['allow_subscribe'] == 't') {
		$auto_email_sql = "select user_id from ".$GLOBALS['BR_proj_auto_mailto_table']." where project_id=".$project_id;
	} else {
		$auto_email_sql = "select user_id from ".$GLOBALS['BR_proj_auto_mailto_table']." where 
			project_id=".$project_id." and can_unsubscribe='f'";
	}
	
	$auto_email_result = $GLOBALS['connection']->Execute($auto_email_sql) or DBError(__FILE__.":".__LINE__);
	while ($row = $auto_email_result->FetchRow()) {
		$user_id = $row["user_id"];
		array_push($cc_array, $user_id);
	}
	$cc_array = ArrayUnique($cc_array);

	$userarray = GetAllUsers(1, 0);
	$to = UidToEmail($userarray, $assign_to);

	$cc_email_array = array();
	for ($i = 0; $i < sizeof($cc_array); $i++) {
		$email = UidToEmail($userarray, $cc_array[$i]);
		if (IsEmailAddress($email) && ($email != $to)) {
			array_push($cc_email_array, $email);
		}
	}
	$cc = implode(",", $cc_email_array);

	$subject = str_replace('@project_name@', $project_name, $STRING['faq_email_subject']);
	$subject = str_replace('@id@', $faq_id, $subject);

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
	$message .= str_replace('@question@', $question, $STRING['faq_email_text']);
	$message = str_replace('@answer@', $answer, $message);
	$message .= "</body></html>";

	$email = $_SESSION[SESSION_PREFIX.'email'];
	if (IsEmailAddress($email)) {
		$from = "=?UTF-8?B?".base64_encode($SYSTEM['mail_from_name']." - ".$_SESSION[SESSION_PREFIX.'username'])."?=<".$email.">";
	} else {
		$from = "=?UTF-8?B?".base64_encode($SYSTEM['mail_from_name']." - ".$_SESSION[SESSION_PREFIX.'username'])."?=<".$SYSTEM['mail_from_email'].">";
	}

	flush();
	$message = str_replace("\r\n", "\n", $message);
	$message = str_replace("\n", "\r\n", $message);
	if ($SYSTEM["mail_function"] == "smtp") {
		$smtp = new smtp($SYSTEM["mail_smtp_server"], $SYSTEM["mail_smtp_port"], 
						 ($SYSTEM["mail_smtp_auth"] == "t")?TRUE:FALSE, 
						 $SYSTEM["mail_smtp_user"], $SYSTEM["mail_smtp_password"]);

		$smtp->debug = $GLOBALS["smtp_debug"];
		$smtp->sendmail($to, $from, $subject, $message, "HTML", $cc);
	} else {
		if (!function_exists('mail')) {
			return -1;
		}
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";

		// additional headers 
		$headers .= "From: ".$from.".\r\n";
		if (strlen($cc) > 0) {
			$headers .= "Cc: ".$cc."\r\n";
		}

		mail($to, $subject, $message, $headers);
	}

	return 0;
}

/* Send schedule email people in $to
 *
 */
function SendScheduleEmail($schedule_id, $to)
{
	global $SYSTEM, $STRING;

	if ($SYSTEM['mail_function'] == 'nosend') {
		return 0;
	}

	if (!$schedule_id) {
		return 0;
	}
	if ($to == "") {
		return 0;
	}

	$sql = "select ".$GLOBALS['BR_schedule_table'].".*, ".$GLOBALS['BR_user_table'].".username
			from ".$GLOBALS['BR_schedule_table'].", ".$GLOBALS['BR_user_table']." 
			where schedule_id=".$schedule_id." and 
			".$GLOBALS['BR_schedule_table'].".created_by=".$GLOBALS['BR_user_table'].".user_id";

	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	$line = $result->Recordcount();
	if ($line != 1) {
		return -1;
	}

	$date = $result->UserTimeStamp($result->fields["date"], GetDateFormat());
	$created_by = $result->fields["username"];
	$created_uid = $result->fields["created_by"];
	$subject = $result->fields["subject"];
	$description = $result->fields["description"];
	$project_id = $result->fields["project_id"];
	$publish = $result->fields["publish"];

	if (($_SESSION[SESSION_PREFIX.'uid'] != 0) && ($created_uid != $_SESSION[SESSION_PREFIX.'uid']) && ($publish != 't')) {
		return -1;
	}
	if ($project_id != 0) {
		$sql = "select project_name from ".$GLOBALS['BR_project_table']." where project_id=".$project_id;
		$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
		$line = $result->Recordcount();
		if ($line != 1) {
			$project_id = 0;
		} else {
			$project_name = $result->fields["project_name"];
		}
	}

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

	$message .= '
	<p>&nbsp;</p>
	<div align="center">
	<center>
	<table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="600">
	<tr>
		<td width="100%" class="title" colspan="2" align="center">&nbsp;
		</td>
	</tr>
	<tr>
		<td width="150" class="prompt" nowrap>
		
		'.$STRING['subject'].$STRING['colon'].'</td>
		<td width="450" class="content">'.$subject.'
		</td>
	</tr>
	<tr>
		<td width="150" class="prompt" nowrap>
        '.$STRING['date'].$STRING['colon'].'</td>
		<td width="450" class="content">'.$date.'
  		</td>
	</tr>
	<tr>
		<td width="150" class="prompt" nowrap>
        '.$STRING['created_by'].$STRING['colon'].'</td>
		<td width="450" class="content">'.$created_by.'
  		</td>
	</tr>
	<tr>
		<td class="prompt">
        '.$STRING['schedule_type'].$STRING['colon'].'</td>
		<td class="content">';
        
	if ($project_id > 0) {
		$message .= $STRING['project_schedule']."(".$project_name.")";
	} else {
		$message .= $STRING['personal_schedule'];
	}
	$message .= '
		</td>
	</tr>
	<tr>
		<td class="prompt" nowrap>
		'.$STRING['publish_schedule'].$STRING['colon'].'</td>
		<td class="content">'.(($publish == 't')?$STRING['yes']:$STRING['no']).'
		</td>
	</tr>
	<tr>
		<td class="prompt" nowrap>
		'.$STRING['description'].$STRING['colon'].'</td>
		<td class="content">'.$description.'
		</td>
	</tr>
	</table></body></div></center></html>';

	$subject = "[".$STRING['title_schedule']."]".$subject;

	$subject = "=?UTF-8?B?". base64_encode($subject)."?=";

	$email = $_SESSION[SESSION_PREFIX.'email'];
	if (IsEmailAddress($email)) {
		$from = "=?UTF-8?B?".base64_encode($SYSTEM['mail_from_name']." - ".$_SESSION[SESSION_PREFIX.'username'])."?=<".$email.">";
	} else {
		$from = "=?UTF-8?B?".base64_encode($SYSTEM['mail_from_name']." - ".$_SESSION[SESSION_PREFIX.'username'])."?=<".$SYSTEM['mail_from_email'].">";
	}

	flush();
	$message = str_replace("\r\n", "\n", $message);
	$message = str_replace("\n", "\r\n", $message);
	if ($SYSTEM["mail_function"] == "smtp") {
		$smtp = new smtp($SYSTEM["mail_smtp_server"], $SYSTEM["mail_smtp_port"], 
						 ($SYSTEM["mail_smtp_auth"] == "t")?TRUE:FALSE, 
						 $SYSTEM["mail_smtp_user"], $SYSTEM["mail_smtp_password"]);

		$smtp->debug = $GLOBALS["smtp_debug"];
		$smtp->sendmail($to, $from, $subject, $message, "HTML");

	} else {
		if (!function_exists('mail')) {
			return -1;
		}
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";

		// additional headers 
		$headers .= "From: ".$from."\r\n";

		mail($to, $subject, $message, $headers);
	}

	return 0;
}

?>
