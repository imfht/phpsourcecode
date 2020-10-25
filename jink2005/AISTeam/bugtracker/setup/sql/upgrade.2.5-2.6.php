<?php
function AlterTableForAutoIncrement()
{
	$table_list = array(
		"login_log_table"=>"login_id",
		"syslog_table" => "syslog_id",
		"feedback_syslog_table" => "syslog_id",
		"customer_table" => "customer_id",
		"customer_user_table" => "customer_user_id",
		"group_table" => "group_id",
		"user_table" => "user_id",
		"document_class_table" => "document_class_id",
		"document_table" => "document_id",
		"document_history_table" => "document_history_id",
		"filter_table" => "filter_id",
		"status_table" => "status_id",
		"project_table" => "project_id",
		"proj_area_table" => "area_id",
		"faq_class_table" => "faq_class_id",
		"faq_content_table" => "faq_id",
		"schedule_table" => "schedule_id"
	);
	$project_table_list = array(
		"proj@PROJECT_ID@_report_table" => "report_id",
		"proj@PROJECT_ID@_report_log_table" => "log_id",
		"proj@PROJECT_ID@_seealso_table" => "ref_id",
		"proj@PROJECT_ID@_feedback_table" => "report_id",
		"proj@PROJECT_ID@_feedback_content_table" => "content_id"
	);

	// Normalize the project tables and put it into $table_list
	$sql = "select project_id from ".$GLOBALS['BR_project_table'];
	$sql_result = $GLOBALS['connection']->Execute($sql);
	if (!$sql_result) {
		$reason = '<p><b>Error: '.$GLOBALS['connection']->ErrorMsg().'</b></p>';
		return $reason;
	}
	while ($row = $sql_result->FetchRow()){
		$project_id = $row['project_id'];

		foreach ($project_table_list as $table=>$column) {
			$table =  str_replace("@PROJECT_ID@", $project_id, $table);
			$table_list[$table] = $column;
		}
	}

	// Avoid MySQL to auto increment when value is 0
	if (strstr($GLOBALS['BR_dbtype'], "mysql")) {
		$result = $GLOBALS['connection']->Execute("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
		if (!$result) {
			$reason = '<p><b>Error: '.$GLOBALS['connection']->ErrorMsg().'</b>'.$sql.'</p>';
			return $reason;
		}
	}

	// Start to upgrade table
	foreach ($table_list as $table=>$column) {
		if (strstr($GLOBALS['BR_dbtype'], "mysql")) {
			$sql = "ALTER TABLE $table MODIFY COLUMN $column int4 not NULL AUTO_INCREMENT;";
			$result = $GLOBALS['connection']->Execute($sql);
		} else {
			$sql = "CREATE SEQUENCE ".$table."_".$column."_seq;";
			$result = $GLOBALS['connection']->Execute($sql);
			if (!$result) {
				$reason = '<p><b>Error: '.$GLOBALS['connection']->ErrorMsg().'</b>'.$sql.'</p>';
				return $reason;
			}
			$sql = "ALTER TABLE $table ALTER $column SET DEFAULT nextval('".$table."_".$column."_seq');";
			$result = $GLOBALS['connection']->Execute($sql);
			if (!$result) {
				$reason = '<p><b>Error: '.$GLOBALS['connection']->ErrorMsg().'</b>'.$sql.'</p>';
				return $reason;
			}
			$max_id_sql = "select max(".$column.") from ".$table;
			$max_id_result = $GLOBALS['connection']->Execute($max_id_sql) or DBError(__FILE__.":".__LINE__);
			$max_id = $max_id_result->fields[0];
			if ($max_id <= 0) {
				$max_id = 1; // Filter table's id is -1 if there is no user's filter
			}

			$sql = "SELECT setval('".$table."_".$column."_seq', ".$max_id.")";
			$result = $GLOBALS['connection']->Execute($sql);
			if (!$result) {
				$reason = '<p><b>Error: '.$GLOBALS['connection']->ErrorMsg().'</b>'.$sql.'</p>';
				return $reason;
			}
		}
		
		if (!$result) {
			$reason = '<p><b>Error: '.$GLOBALS['connection']->ErrorMsg().'</b>'.$sql.'</p>';
			return $reason;
		}
	}
}

$reason = AlterTableForAutoIncrement();

?>

