<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: status_function.php,v 1.4 2008/11/28 10:36:49 alex Exp $
 *
 */
function GetStatusArray()
{
	$status_sql = "select * from ".$GLOBALS['BR_status_table']." order by status_name";
	$status_result = $GLOBALS['connection']->Execute($status_sql) or DBError(__FILE__.":".__LINE__);

	$statusarray = array();
	while ($row = $status_result->FetchRow()) {
		$status_id = $row["status_id"];
		$status_name = $row["status_name"];
		$status_color = $row["status_color"];
		$status_type = $row["status_type"];

		$new_status = new statusclass;
		$new_status->setstatusid($status_id);
		$new_status->setstatusname($status_name);
		$new_status->setstatuscolor($status_color);
		$new_status->setstatustype($status_type);
		array_push($statusarray, $new_status);
	}
	
	return $statusarray;
}

function GetStatusClassByID($status_array, $status_id)
{
	for ($i = 0; $i < sizeof($status_array); $i++) {
		if ($status_array[$i]->getstatusid() == $status_id) {
			return $status_array[$i];
		}
	}
	return NULL;
}

function GetStatusNameByID($status_array, $status_id)
{
	for ($i = 0; $i < sizeof($status_array); $i++) {
		if ($status_array[$i]->getstatusid() == $status_id) {
			return $status_array[$i]->getstatusname();
		}
	}
	return "";
}

class statusclass {
	var $status_id;
	var $status_name;
	var $status_color;
	var $status_type;
	
	function setstatusid($id) {
		$this->status_id = $id;
	}
	function setstatusname($status) {
		$this->status_name = $status;
	}
	function setstatuscolor($color) {
		$this->status_color = $color;
	}
	function setstatustype($type) {
		$this->status_type = $type;
	}
	function getstatusid() {
		return $this->status_id;
	}
	function getstatusname() {
		return $this->status_name;
	}
	function getstatuscolor() {
		return $this->status_color;
	}
	function getstatustype() {
		return $this->status_type;
	}
}
?>
