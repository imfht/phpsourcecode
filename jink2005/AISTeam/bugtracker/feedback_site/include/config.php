<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: config.php,v 1.20 2009/05/03 09:20:53 alex Exp $
 *
 */
$GLOBALS['BR_dbserver'] = "127.0.0.1";		// IP Address of database server
$GLOBALS['BR_dbtype'] = "postgres";		// Type of database (postgres, mysqlt)
//$GLOBALS['BR_dbtype'] = "mysqlt";		// Type of database (postgres, mysqlt)
$GLOBALS['BR_dbuser'] = "root";			// User name to access the database
$GLOBALS['BR_dbpwd'] = "";		// Database password
$GLOBALS['BR_dbname'] = "bugdb";		// Database name for bug tracker

$GLOBALS['BR_dbdebug'] = false;
//$GLOBALS['BR_dbdebug'] = true;


/* ============= No need to change anything below ============================*/

/* Disable notice */
$error_reporting = ini_get('error_reporting');
error_reporting($error_reporting &  ~E_NOTICE);

if (strstr($GLOBALS['BR_dbtype'], "postgres")) {
	define('PATTERN_KEYWORD', 'ilike');
} else {
	define('PATTERN_KEYWORD', 'like');
}

define('ITEMS_PER_PAGE', '100');

/* 
 *	1: Save upload files/docs in database.
 *	0: save upload files/docs in file system.
 *
 *	I recommand save files in database. It will have better
 *	security control. If you save files in filesystem, users
 *	could assess the file by http://your_url/upload/project1/filename
 *	
 */
$GLOBALS['SYS_FILE_IN_DB'] = 1;

// Database table defination
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
$GLOBALS['BR_document_table'] = "document_table";
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

// Data define
$GLOBALS['priority_array'] = array(" ", "priority_very_low", "priority_low", "priority_normal", "priority_high", "priority_very_high");
$GLOBALS['priority_color'] = array("gray", "#525252","#2f4f4f", "black", "#FF7F50", "red");
$GLOBALS['type_array'] = array("", "type_bug","type_feature","type_usability","type_document");
$GLOBALS['reproducibility_array'] = array("reproducibility_ididnttry", "reproducibility_rarely", "reproducibility_sometimes", "reproducibility_always");
$GLOBALS['feedback_status'] = array(" ", "Open", "In process", "Suspended", "Closed");
$GLOBALS['feedback_status_color'] = array("gray", "red", "#330099", "#2f4f4f", "gray");

$privilege_array = array("can_admin_user", "can_admin_customer", 
						 "can_create_project", "can_update_project", 
						 "can_delete_project", "can_create_report", 
						 "can_update_report", "can_delete_report", 
						 "can_admin_feedback", "can_admin_faq",
						 "can_admin_status", "can_see_document",
						 "can_create_document", "can_update_document",
						 "can_delete_document", "can_edit_selfdata");
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
					  ".png" => "image/x-png",
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

define('SESSION_PREFIX', 'BR_reg_feedback_');
?>
