<?php
function AlterProjectTable21()
{
	$reason = "";
	if (strstr($GLOBALS['BR_dbtype'], "mysql")) {
		$sql = "select project_id from ".$GLOBALS['BR_project_table'];
		$sql_result = $GLOBALS['connection']->Execute($sql);
		if (!$sql_result) {
			$reason = '<p><b>Error: '.$GLOBALS['connection']->ErrorMsg().'</b></p>';
		}
		$sqls = array("ALTER TABLE proj@PROJECT_ID@_report_table MODIFY COLUMN created_date DATETIME;",
					  "ALTER TABLE proj@PROJECT_ID@_report_table MODIFY COLUMN fixed_date DATETIME;",
					  "ALTER TABLE proj@PROJECT_ID@_report_table MODIFY COLUMN verified_date DATETIME;",
					  "ALTER TABLE proj@PROJECT_ID@_report_table MODIFY COLUMN estimated_time DATETIME;",
					  "ALTER TABLE proj@PROJECT_ID@_report_table MODIFY COLUMN last_update DATETIME;",
					  "ALTER TABLE proj@PROJECT_ID@_report_log_table MODIFY COLUMN post_time DATETIME;",
					  "ALTER TABLE proj@PROJECT_ID@_feedback_table MODIFY COLUMN created_date DATETIME;",
					  "ALTER TABLE proj@PROJECT_ID@_feedback_content_table MODIFY COLUMN post_time DATETIME;");
		while ($row = $sql_result->FetchRow()){
			$project_id = $row['project_id'];

			for ($k = 0; $k < sizeof($sqls); $k++) {
				$sql = str_replace("@PROJECT_ID@", $project_id, $sqls[$k]);
				echo "<b>$project_id $sql</b></br>";
				$result = $GLOBALS['connection']->Execute($sql);

				if (!$result) {
					$reason = '<p><b>Error: '.$GLOBALS['connection']->ErrorMsg().'</b>'.$sql.'</p>';
					break;
				}
			}
		}
	}
	return $reason;
}

$reason = AlterProjectTable21();
?>
