<?php
/* Copyright (c) 2003-2005 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: project_function.php,v 1.18 2013/07/07 21:31:13 alex Exp $
 *
 */
class projectclass {
	var $project_id;
	var $project_name;
	var $last_read;
	function setprojectid($id) {
		$this->project_id = $id;
	}
	function setprojectname($name) {
		$this->project_name = $name;
	}
	function setlastread($inputdate) {
		$this->last_read=$inputdate;
	}
	function getprojectid() {
		return $this->project_id;
	}
	function getprojectname() {
		return $this->project_name;
	}
	function getlastread() {
		return $this->last_read;
	}
}

function GetAllProjects($user_id = "")
{
	if (($user_id == "") || ($user_id == 0)) {
		$all_project_sql = "select * from ".$GLOBALS['BR_project_table']." order by project_name";
	} else {
		$all_project_sql = "select A.* from ".$GLOBALS['BR_project_table']." as A, ".$GLOBALS['BR_proj_access_table']." as B ". 
        "where A.project_id=B.project_id and B.user_id=".$user_id." order by project_name"; 
	}
	$all_project_result = $GLOBALS['connection']->Execute($all_project_sql) or DBError(__FILE__.":".__LINE__);
	$project_array = array();
	while ($all_project_row = $all_project_result->FetchRow()) {
		$project_id = $all_project_row["project_id"];
		$project_name = $all_project_row["project_name"];
		$new_project = new projectclass;
		$new_project->setprojectid($project_id);
		$new_project->setprojectname($project_name);
		array_push($project_array, $new_project);
	}
	$all_project_result->Free();

	return $project_array;
}

function GetProjectFromID($project_array, $project_id)
{
	for ($i = 0; $i < sizeof($project_array); $i++) {
		if ($project_array[$i]->getprojectid() == $project_id) {
			return $project_array[$i];
		}
	}
	return "";
}

$ProjectAccessArray = array(array());
$AllProjectID = array();
function CheckProjectAccessable($project_id, $user_id)
{
	global $ProjectAccessArray;
	global $AllProjectID;

	if (($project_id === "") || ($user_id === "") || !is_numeric($project_id) || !is_numeric($user_id)){
		return FALSE;
	}
	if ($user_id != 0) {
		/* Read all users that can access this project in the memory
		 * to avoid reduce the time to access disk.
		 */
		if (!isset($ProjectAccessArray[$project_id])) {
			$get_access_sql = "select user_id from ".$GLOBALS['BR_proj_access_table']." 
						where project_id=".$GLOBALS['connection']->QMagic($project_id);
			$get_access_result = $GLOBALS['connection']->Execute($get_access_sql) or DBError(__FILE__.":".__LINE__);
			$count = 0;
			while ($row = $get_access_result->FetchRow()){
				$this_id = $row['user_id'];
				$ProjectAccessArray[$project_id][$count] = $this_id;
				$count++;
			}
		}
		if (IsInArray($ProjectAccessArray[$project_id], $user_id) == -1) {
			return FALSE;
		} else {
			return TRUE;
		}
	} else {
		if (sizeof($AllProjectID) == 0) {
			$get_project_sql = "select project_id from ".$GLOBALS['BR_project_table'];
			$project_result = $GLOBALS['connection']->Execute($get_project_sql) or DBError(__FILE__.":".__LINE__);
			while ($row = $project_result->FetchRow()){
				$this_id = $row['project_id'];
				array_push($AllProjectID, $this_id);
			}
		}
		if (IsInArray($AllProjectID, $project_id) == -1) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
}

define("SEARCH_TYPE_CONTENT", 1);
define("SEARCH_TYPE_AREA", 2);
function ConditionByFilterSearch($chosen_filter, $label, $search_key, $search_type=SEARCH_TYPE_CONTENT)
{
	$condition = "";

	if ($chosen_filter != 0) {
		// Check whether the filter is valid
		$get_filter_sql = "select * from ".$GLOBALS['BR_filter_table']." where filter_id=".$GLOBALS['connection']->QMagic($chosen_filter);
		$get_filter_result = $GLOBALS['connection']->Execute($get_filter_sql) or DBError(__FILE__.":".__LINE__);
		$line = $get_filter_result->Recordcount();
		if ($line != 1) {
			$chosen_filter=0;
		} else {
			$condition = $get_filter_result->fields["real_condition"];
			if ($chosen_filter < 0) {
				// Default filter
				$condition = str_replace("@UID@", $_SESSION[SESSION_PREFIX.'uid'], $condition);

				$last_sun = $GLOBALS['connection']->DBTimeStamp(time() - (date("w")*86400) - 7*86400);
				$condition = str_replace("@LAST_SUN@", $last_sun, $condition);

				$last_sat = $GLOBALS['connection']->DBTimeStamp(time() - (date("w")*86400));
				$condition = str_replace("@LAST_SAT@", $last_sat, $condition);

			}
		}
	}
	$search_condition = "";

	if ($search_key != "") {
		if ($search_type == SEARCH_TYPE_AREA) {
			$area_match="(area ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$search_key."%").")";
			$minor_area_match="(minor_area ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$search_key."%").")";
			if (strstr($search_key, " and ")) {
				$area_match=str_replace(" and ","%' and area ".PATTERN_KEYWORD." '%", $area_match);
				$minor_area_match=str_replace(" and ","%' and minor_area ".PATTERN_KEYWORD." '%", $minor_area_match);
			}
			if (strstr($search_key, " not ")) {
				$area_match=str_replace(" not ","%' and area not ".PATTERN_KEYWORD." '%", $area_match);
				$minor_match=str_replace(" not ","%' and minor_area not ".PATTERN_KEYWORD." '%", $minor_area_match);
			}
			if (strstr($search_key, " or ")) {
				$area_match = str_replace(" or ","%' or area ".PATTERN_KEYWORD." '%", $area_match);
				$minor_area_match = str_replace(" or ","%' or minor_area ".PATTERN_KEYWORD." '%", $minor_area_match);
			}
			$search_condition = "$area_match or $minor_area_match";
		} else {
			$summary_match="(summary ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$search_key."%").")";
			$log_match="(description ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$search_key."%").")";
			if (strstr($search_key, " and ")) {   
				$summary_match=str_replace(" and ","%' and summary ".PATTERN_KEYWORD." '%",$summary_match);
				$log_match=str_replace(" and ","%' and description ".PATTERN_KEYWORD." '%",$log_match);
			}
			if (strstr($search_key, " not ")) {
				$summary_match=str_replace(" not ","%' and summary not ".PATTERN_KEYWORD." '%",$summary_match);
				$log_match=str_replace(" not ","%' and description not ".PATTERN_KEYWORD." '%",$log_match);
			}
			if (strstr($search_key, " or ")) {
				$summary_match=str_replace(" or ","%' or summary ".PATTERN_KEYWORD." '%",$summary_match);
				$log_match=str_replace(" or ","%' or description ".PATTERN_KEYWORD." '%",$log_match);
			}
			$search_condition = $summary_match." or ( report_id in 
				(select report_id from proj".$_REQUEST['project_id']."_report_log_table where ".$log_match."
				group by report_id))";
		}
	}
	if ($label) {
		$label_condition = "report_id in (SELECT report_id FROM ".$GLOBALS['BR_label_mapping_table']." WHERE label_id=".$GLOBALS['connection']->QMagic($label).")";
		if ($condition == "") {
			$condition = $label_condition;
		} else {
			$condition = "(".$label_condition.") and (".$condition.")";
		}
	}
	
	if (($condition == "") && ($search_condition != "")) {
		$condition = $search_condition;
	} else if (($condition != "") && ($search_condition != "")) {
		$condition = "(".$search_condition.") and (".$condition.")";
	}
	return $condition;
}

function PrintLabel($project_id, $bugid)
{
	global $label_color_array;
	$sql = "select report_id, ".$GLOBALS['BR_label_mapping_table'].".label_id as label_id, label_name, color from ".$GLOBALS['BR_label_mapping_table'].
			",".$GLOBALS['BR_label_table']." where ".$GLOBALS['BR_label_table'].".project_id=".$project_id." and
			report_id=".$bugid." and ".$GLOBALS['BR_label_mapping_table'].".label_id=".$GLOBALS['BR_label_table'].".label_id
			ORDER BY label_name ASC";
		
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	while ($row = $result->FetchRow()) {
		echo '<div id="label:'.$row['report_id'].':'.$row['label_id'].'" class="report_label" style="color:'.$label_color_array[$row['color']][0].';background-color:'.$label_color_array[$row['color']][1].'">'.
			$row['label_name'].'</div>';
	}

	return;
}

function PrintLabelColorArray($project_id)
{
	global $label_color_array;
	$sql = "select label_id, label_name, color from ".$GLOBALS['BR_label_table'].
		" where ".$GLOBALS['BR_label_table'].".project_id=".$project_id;
		
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	echo "[";
	$first=true;
	while ($row = $result->FetchRow()) {
		if ($first) {
			$first = false;
		} else {
			echo ",";
		}
		echo "{
			
				label_id:".$row['label_id'].",
				font_color: '".$label_color_array[$row['color']][0]."',
				background_color: '".$label_color_array[$row['color']][1]."'
			}";
	}
	echo "]";

	return;
}
?>
