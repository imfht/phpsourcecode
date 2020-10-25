<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: project_function.php,v 1.6 2013/07/07 21:25:44 alex Exp $
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

function GetAllProjects()
{
	$all_project_sql = "select * from ".$GLOBALS['BR_project_table']." order by project_name";
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

function CheckProjectAccessable($project_id, $customer_id)
{
	if (($project_id === "") || ($customer_id === "")) {
		return FALSE;
	}
	if (!is_numeric($project_id) || !is_numeric($customer_id)) {
		return FALSE;
	}
	$check_auth_sql = "select * from ".$GLOBALS['BR_proj_customer_access_table']." 
				where customer_id=".$GLOBALS['connection']->QMagic($customer_id)." and 
				project_id=".$GLOBALS['connection']->QMagic($project_id);
	$check_auth_result = $GLOBALS['connection']->Execute($check_auth_sql) or DBError(__FILE__.":".__LINE__);
	$line = $check_auth_result->Recordcount();
	if ($line != 1) {
		return FALSE;
	} else {
		return TRUE;
	}
}
?>
