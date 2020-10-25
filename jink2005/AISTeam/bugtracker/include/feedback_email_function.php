<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: feedback_email_function.php,v 1.17 2013/07/07 21:31:13 alex Exp $
 *
 */
/*
 * The $extra_cc format is "1,2,3" (user_id, separated by ",")
 *
 */
include_once("smtp.php");
include("feedback_report_function.php");

function SendUpdateCustomerUserEamil($email, $password, $type)
{
	global $SYSTEM;

	if ($SYSTEM['mail_function'] == 'nosend') {
		return 0;
	}

	$sql = "select language from customer_user_table where email=".$GLOBALS['connection']->QMagic($email);
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	if ($result->Recordcount() != 1) {		
		return -1;
	}
	$language = $result->fields["language"];

	$STRING = GetStringArray($language, false);

	$system_sql = "select * from ".$GLOBALS['BR_feedback_config_table'];
	$system_result = $GLOBALS['connection']->Execute($system_sql) or die(__FILE__.":".__LINE__);
	$line = $system_result->Recordcount();
	if ($line != 1) {
		die("Failed to get feedback system configuration.");
	}

	$feedback_system_name = $system_result->fields["feedback_system_name"];
	$mail_from_name = $system_result->fields["mail_from_name"];
	$mail_from_email = $system_result->fields["mail_from_email"];

	if (!IsEmailAddress($email)) {
		return -1;
	}
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
		$subject = str_replace('@program_name@', $feedback_system_name, $STRING['feedback_update_user_email_subject']);
		$message .= str_replace('@program_name@', $feedback_system_name, $STRING['feedback_update_user_email_text']);
	} else {
		$subject = str_replace('@program_name@', $feedback_system_name, $STRING['feedback_new_user_email_subject']);
		$message .= str_replace('@program_name@', $feedback_system_name, $STRING['feedback_new_user_email_text']);
	}
	$subject = "=?UTF-8?B?".base64_encode($subject)."?=";

	$message = str_replace('@username@', $email, $message);
	$message = str_replace('@password@', $password, $message);
	$message = str_replace('@mail_from_name@', $mail_from_name, $message);
	$message .= '</body></html>';

	if ($SYSTEM["mail_function"] == "smtp") {
		$smtp = new smtp($SYSTEM["mail_smtp_server"], $SYSTEM["mail_smtp_port"], 
						 ($SYSTEM["mail_smtp_auth"] == "t")?TRUE:FALSE, 
						 $SYSTEM["mail_smtp_user"], $SYSTEM["mail_smtp_password"]);

		$smtp->debug = $GLOBALS["smtp_debug"];
		$smtp->sendmail($email, "=?UTF-8?B?".base64_encode($mail_from_name)."?=<".$mail_from_email.">", $subject, $message, "HTML");
	} else {
		if (!function_exists('mail')) {
			return -1;
		}
		$headers  = "MIME-Version: 1.0 \n";
		$headers .= "Content-type: text/html; charset=utf-8 \n";
		$headers .= "From: =?UTF-8?B?".base64_encode($mail_from_name)."?=<".$mail_from_email."> \n";
		mail($email, $subject, $message, $headers);
	}
	
	return 0;
}

function SendFeedbackReportEmail($project_id, $report_id, $extra_cc = "")
{
	global $SYSTEM;

	if ($SYSTEM['mail_function'] == 'nosend') {
		return 0;
	}

	$bcc_array = array();
	$cc_array = array();
	$to_array = array();

	$userarray = GetAllUsers(0, 0);

	// Get the project name
	$project_sql = "select project_name from ".$GLOBALS['BR_project_table']." where project_id=".$project_id;
	$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
	if ($project_result->Recordcount() != 1) {
		return -1;
	}
	$project_name = $project_result->fields["project_name"];

	$extra_cc_array = explode(",", $extra_cc);
	for ($i = 0; $i < sizeof($extra_cc_array); $i++) {
		$email = UidToEmail($userarray, $extra_cc_array[$i]);
		if (IsEmailAddress($email)) {
			array_push($cc_array, $email);
		}
	}
	// CC list, Get auto mail to(customer support staff in the company) of the project
	$auto_email_sql = "select user_id from ".$GLOBALS['BR_proj_feedback_mailto_table']." where project_id=".$project_id;
	$auto_email_result = $GLOBALS['connection']->Execute($auto_email_sql) or DBError(__FILE__.":".__LINE__);
	while ($row = $auto_email_result->FetchRow()) {
		$user_id = $row["user_id"];
		
		$email = UidToEmail($userarray, $user_id);
		if (IsEmailAddress($email)) {
			array_push($cc_array, $email);
		}
	}
	// Get auto mail to(bcc list) of the project
	$auto_email_sql = "select user_id from ".$GLOBALS['BR_proj_auto_mailto_table']." where project_id=".$project_id;
	$auto_email_result = $GLOBALS['connection']->Execute($auto_email_sql) or DBError(__FILE__.":".__LINE__);
	while ($row = $auto_email_result->FetchRow()) {
		$user_id = $row["user_id"];
		$email = UidToEmail($userarray, $user_id);
		if (IsEmailAddress($email)) {
			array_push($bcc_array, $email);
		}
	}
						
	// Get the data of this report
	$report_sql = "select * from proj".$project_id."_feedback_table where report_id=".$report_id;
	$report_result = $GLOBALS['connection']->Execute($report_sql) or DBError(__FILE__.":".__LINE__);
	if ($report_result->Recordcount() != 1) {
		return -1;
	}
	$summary = $report_result->fields["summary"];
	$cust_report_id = $report_result->fields["cust_report_id"];
	$customer_id = $report_result->fields["customer_id"];
	if ($customer_id == "") {
		// customer has been deleted
		return;
	}
	$priority = $report_result->fields["priority"];
	$status = $report_result->fields["status"];
	$created_by = $report_result->fields["created_by"];
	$disabled_sql = "select count(customer_user_id) from ".$GLOBALS['BR_customer_user_table']." where email=".$GLOBALS['connection']->QMagic($created_by)." and account_disabled!='t'";
	$disabled_result = $GLOBALS['connection']->Execute($disabled_sql) or DBError(__FILE__.":".__LINE__);
	$count = $disabled_result->fields[0];
	if ($count != 0) {
		array_push($to_array, $created_by);
	}

	// Get other users in the same company that should be cc to
	$auto_email_sql = "select email from ".$GLOBALS['BR_customer_user_table']." 
			where customer_id=".$customer_id." and auto_cc_to='t'";
	$auto_email_result = $GLOBALS['connection']->Execute($auto_email_sql) or DBError(__FILE__.":".__LINE__);
	while ($row = $auto_email_result->FetchRow()) {
		$email = $row["email"];

		$disabled_sql = "select count(customer_user_id) from ".$GLOBALS['BR_customer_user_table']." where email=".$GLOBALS['connection']->QMagic($email)." and account_disabled!='t'";
		$disabled_result = $GLOBALS['connection']->Execute($disabled_sql) or DBError(__FILE__.":".__LINE__);
		$count = $disabled_result->fields[0];
		if ($count == 0) {
			continue;
		}
		if (IsEmailAddress($email)) {
			array_push($cc_array, $email);
		}
	}

	$to_array = ArrayUnique($to_array);
	$cc_array = ArrayUnique($cc_array);
	$bcc_array = ArrayUnique($bcc_array);
	
	$tmp_cc_array = array();
	for ($i = 0; $i < sizeof($cc_array); $i++) {
		$email = $cc_array[$i];
		if (IsInArray($to_array, $email) == -1) {
			array_push($tmp_cc_array, $email);
		} else {
			
		}
	}
	$cc_array = $tmp_cc_array;

	$tmp_bcc_array = array();
	for ($i = 0; $i < sizeof($bcc_array); $i++) {
		$email = $bcc_array[$i];
		if ((IsInArray($to_array, $email) == -1) && 
			(IsInArray($cc_array, $email) == -1)) {
			array_push($tmp_bcc_array, $email);
		}
	}
	$bcc_array = $tmp_bcc_array;

	$to = implode(",", $to_array);
	$cc = implode(",", $cc_array);
	$bcc = implode(",", $bcc_array);

	$subject="[".$project_name."][id:".$cust_report_id."][".$GLOBALS['feedback_status'][$status]."] ".$summary;

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
	$message .= GetFeedbackReportOutput($project_id, $report_id, "email");
	$message .= "</body></html>";

	
	if ($priority == 5) {
		$additional_headers .= "X-Priority: 1\n";
		$additional_headers .= "X-MSMail-Priority: High\n";
	}
    
	$feedback_system_sql = "select * from ".$GLOBALS['BR_feedback_config_table'];
	$feedback_system_result = $GLOBALS['connection']->Execute($feedback_system_sql) or die(__FILE__.":".__LINE__);
	$line = $feedback_system_result->Recordcount();
	if ($line != 1) {
		die("Failed to get system configuration.");
	}
	$FEEDBACK_SYSTEM = $feedback_system_result->FetchRow();

	if (!IsEmailAddress($SYSTEM['mail_from_email'])) {
		$FEEDBACK_SYSTEM['mail_from_email'] = "root@".$_SERVER['SERVER_NAME'];
	}
	
	if ($SYSTEM["mail_function"] == "smtp") {
		$smtp = new smtp($SYSTEM["mail_smtp_server"], $SYSTEM["mail_smtp_port"], 
						 ($SYSTEM["mail_smtp_auth"] == "t")?TRUE:FALSE, 
						 $SYSTEM["mail_smtp_user"], $SYSTEM["mail_smtp_password"]);

		$smtp->debug = $GLOBALS["smtp_debug"];

		$smtp->sendmail($to, "=?UTF-8?B?".base64_encode($FEEDBACK_SYSTEM['mail_from_name'])."?=<".$FEEDBACK_SYSTEM['mail_from_email'].">", 
						$subject, $message, "HTML", $cc, $bcc, $additional_headers);
	} else {
		if (!function_exists('mail')) {
			return -1;
		}
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=utf-8\n";
		$headers .= "From: =?UTF-8?B?".base64_encode($FEEDBACK_SYSTEM['mail_from_name'])."?=<".$FEEDBACK_SYSTEM['mail_from_email'].">\n";
		$headers .= "Cc: ".$cc."\n";
		$headers .= "Bcc: ".$bcc."\n";
		$headers .= $additional_headers;

		mail($to, $subject, $message, $headers);
	}
	
	return 0;
}

?>
