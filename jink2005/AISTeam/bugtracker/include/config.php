<?php
/* Copyright 2003-2008 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: config.php,v 1.56 2013/07/07 21:41:32 alex Exp $
 *
 */
$SYSTEM_VERSION = "2.7.1";

$GLOBALS['BR_dbserver'] = "127.0.0.1";		// IP Address of database server
$GLOBALS['BR_dbtype'] = "postgres";		// Type of database (postgres, mysqlt, mysqli)
//$GLOBALS['BR_dbtype'] = "mysqlt";		// Type of database (postgres, mysqlt, mysqli)
$GLOBALS['BR_dbuser'] = "root";			// User name to access the database
$GLOBALS['BR_dbpwd'] = "";		// Database password
$GLOBALS['BR_dbname'] = "bugdb";		// Database name for bug tracker

/* Real path of bug tracker project on your system.
 *
 * For windows platform (Notice: you have to use capital C,D,.. for
 * the driver name:
 * $GLOBALS["SYS_PROJECT_PATH"] = 'D:\www\bug';
 *
 * If you are using Synology Disk Station, please set this to
 * "/volume1/web/bug" if you put Bug Tracker in the web share.
 */
$GLOBALS["SYS_PROJECT_PATH"] = '/home/synosrc/bug';

/* The path of URL. if you have to connect to the Bug Tracker by
 * http://192.168.0.1/bug/index.php, you should input "/bug" here. 
 */
$GLOBALS["SYS_URL_ROOT"] = "/bug";

/* Set to true if you want to debug */
$GLOBALS['BR_dbdebug'] = false;
//$GLOBALS['BR_dbdebug'] = true;

/* This option is used to debug SMTP function. If you can't receive mail sent
 * by Bug Tracker, set this option to "TRUE" to debug. Remember to turn it off
 * by set to "FALSE" after debug.
 */
$GLOBALS["smtp_debug"] = FALSE;


/* ============= No need to change anything below ============================*/

/* Disable notice */
$error_reporting = ini_get('error_reporting');
error_reporting($error_reporting &  ~E_NOTICE);

if (strstr($GLOBALS['BR_dbtype'], "postgres")) {
	define('PATTERN_KEYWORD', 'ilike');
} else {
	define('PATTERN_KEYWORD', 'like');
}
/* 
 *	1: Save upload files/docs in database.
 *	0: save upload files/docs in file system.
 *
 *	I recommand save files in database. It will have better
 *	security control. If you save files in filesystem, users
 *	could assess the file by http://your_url/report/upload/project1/filename
 *	
 *  If you would like to save files in filesystem rather than
 *  database in order to get better performance, please create a
 *  directory "report/upload" and "document/documents". If your
 *  are using Unix-like server, remember to chmod the "report/upload" and
 *  "document/documents" to grant write permission
 *  to httpd user.
 *
 */
$GLOBALS['SYS_FILE_IN_DB'] = 1;

// Database table define
$GLOBALS['BR_sysconf_table'] = "sysconf_table";
$GLOBALS['BR_feedback_config_table'] = "feedback_config_table";
$GLOBALS['BR_feedback_syslog_table'] = "feedback_syslog_table";
$GLOBALS['BR_language_table'] = "language_table";
$GLOBALS['BR_string_table'] = "string_table";
$GLOBALS['BR_login_log_table'] = "login_log_table";
$GLOBALS['BR_syslog_table'] = "syslog_table";
$GLOBALS['BR_customer_table'] = "customer_table";
$GLOBALS['BR_customer_user_table'] = "customer_user_table";
$GLOBALS['BR_customer_user_tmp_table'] = "customer_user_tmp_table";
$GLOBALS['BR_user_table'] = "user_table";
$GLOBALS['BR_group_table'] = "group_table";
$GLOBALS['BR_project_table'] = "project_table";
$GLOBALS['BR_document_class_table'] = "document_class_table";
$GLOBALS['BR_document_map_table'] = "document_map_table";
$GLOBALS['BR_document_table'] = "document_table";
$GLOBALS['BR_document_history_table'] = "document_history_table";
$GLOBALS['BR_filter_table'] = "filter_table";
$GLOBALS['BR_status_table'] = "status_table";
$GLOBALS['BR_group_allow_status_table'] = "group_allow_status_table";
$GLOBALS['BR_proj_access_table'] = "proj_access_table";
$GLOBALS['BR_proj_customer_access_table'] = "proj_customer_access_table";
$GLOBALS['BR_proj_area_table'] = "proj_area_table";
$GLOBALS['BR_proj_auto_mailto_table'] = "proj_auto_mailto_table";
$GLOBALS['BR_proj_feedback_mailto_table'] = "proj_feedback_mailto_table";
$GLOBALS['BR_faq_class_table'] = "faq_class_table";
$GLOBALS['BR_faq_content_table'] = "faq_content_table";
$GLOBALS['BR_faq_map_table'] = "faq_map_table";
$GLOBALS['BR_schedule_table'] = "schedule_table";
$GLOBALS['BR_label_table'] = "label_table";
$GLOBALS['BR_label_mapping_table'] = "label_mapping_table";

// Data defination
$GLOBALS['priority_array'] = array(" ", "priority_very_low", "priority_low", "priority_normal", "priority_high", "priority_very_high");
$GLOBALS['priority_color'] = array("gray", "#525252","#2f4f4f", "black", "#FF7F50", "red");
$GLOBALS['type_array'] = array("", "type_bug","type_feature","type_usability","type_document");
$GLOBALS['reproducibility_array'] = array("reproducibility_ididnttry", "reproducibility_rarely", "reproducibility_sometimes", "reproducibility_always");
$GLOBALS['feedback_status'] = array(" ", "Open", "In process", "Suspended", "Closed");
$GLOBALS['feedback_status_color'] = array("gray", "red", "#330099", "#2f4f4f", "gray");

/* Max 32 privilege type (we use an integer to save privilege). Do not change its order.
 * Otherwise, we would have compatibility issue when upgrade. */
$privilege_array = array("can_admin_user", "can_admin_customer", 
						 "can_create_project", "can_update_project", 
						 "can_delete_project", "can_create_report", 
						 "can_update_report", "can_delete_report", 
						 "can_admin_feedback", "can_admin_faq",
						 "can_admin_status", "can_see_document",
						 "can_create_document", "can_update_document",
						 "can_delete_document", "can_edit_selfdata",
						 "can_see_schedule", "can_edit_schedule",
						 "can_see_sysinfo", "can_see_statistic",
						 "can_manage_document_class",
						 "can_manage_label");
$privilege_display_array = array("can_admin_user", "can_admin_customer", 
						 "can_create_project", "can_update_project", 
						 "can_delete_project", "can_create_report", 
						 "can_update_report", "can_delete_report",
						 "can_manage_label",
						 "can_admin_feedback", "can_admin_faq",
						 "can_admin_status", "can_see_document",
						 "can_create_document", "can_update_document",
						 "can_delete_document", "can_manage_document_class",
						 "can_edit_selfdata", "can_see_schedule", 
						 "can_edit_schedule", "can_see_sysinfo", 
						 "can_see_statistic");

$show_column_array = array("reported_by", "created_date", "assign_to",
						   "priority", "status",
						   "fixed_by", "fixed_date",
						   "version", "fixed_in_version", "verified_by",
						   "verified_date", "area",
						   "minor_area", "estimated_time",
						   "type", "reported_by_customer");

$log_type_array = array("", "info", "warn", "error");

$reserve_words = "'\$,\"*<>()\\";

$content_type = array(".html" => "text/html", 
					  ".htm" => "text/html",
					  ".txt" => "text/plain ",
					  ".c" => "text/plain ",
					  ".cpp" => "text/plain ",
					  ".h" => "text/plain ",
					  ".cc" => "text/plain ",
					  ".css" => "text/css",
					  ".gif" => "image/gif",
					  ".png" => "image/png",
					  ".jpg" => "image/jpeg",
					  ".jpeg" => "image/jpeg",
					  ".tiff" => "image/tiff",
					  ".bmp" => "image/bmp",
					  ".rtf" => "application/msword",
					  ".doc" => "application/msword",
					  ".xls" => "application/vnd.ms-excel",
					  ".ppt" => "application/vnd.ms-powerpoint",
					  ".zip" => "application/x-zip",
					  ".pdf" => "application/pdf",
					  ".tgz" => "application/x-tar"
					  );

/* font_color, bg_color */
$label_color_array = array(
	array("#5A6986","#DEE5F2"),
	array("#206CFF","#E0ECFF"),
	array("#0000CC","#DFE2FF"),
	array("#5229A3","#FDE9F4"),
	array("#854F61","#FFE3E3"),
	array("#CC0000","#FFE3E3"),
	array("#DEE5F2","#5A6986"),
	array("#E0ECFF","#206CFF"),
	array("#DFE2FF","#0000CC"),
	array("#E0D5F9","#5229A3"),
	array("#FDE9F4","#854F61"),
	array("#FFE3E3","#CC0000"),
	array("#EC7000","#FFF0E1"),
	array("#B36D00","#FADCB3"),
	array("#AB8B00","#F3E7B3"),
	array("#636330","#FFFFD4"),
	array("#64992C","#F9FFEF"),
	array("#006633","#F1F5EC"),
	array("#FFF0E1","#EC7000"),
	array("#FADCB3","#B36D00"),
	array("#F3E7B3","#AB8B00"),
	array("#FFFFD4","#636330"),
	array("#F9FFEF","#64992C"),
	array("#F1F5EC","#006633")
	);
define('SESSION_PREFIX', 'BR_reg_');
?>
