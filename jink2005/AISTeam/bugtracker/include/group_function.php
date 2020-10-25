<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: group_function.php,v 1.7 2013/07/07 21:31:13 alex Exp $
 *
 */
$GLOBALS['Privilege'] = 0;

function GetGroupPrivilege($group_id)
{
	if (!is_numeric($group_id)) {
		return 0;
	}

	$group_sql = "select privilege from ".$GLOBALS['BR_group_table']." where group_id=".$GLOBALS['connection']->QMagic($group_id);
	$group_result = $GLOBALS['connection']->Execute($group_sql) or DBError(__FILE__.":".__LINE__);

	$Privilege = $group_result->fields["privilege"];
	if (!is_numeric($Privilege)) {
		$Privilege = 0;
	}

	return $Privilege;
}

function InitGroupPrivilege($group_id)
{
	global $privilege_array;

	if (!is_numeric($group_id)) {
		return 0;
	}

	for ($i = 0; $i< sizeof($privilege_array); $i++) {
		$GLOBALS[$privilege_array[$i]] = 1 << $i;
	}
			   
	$GLOBALS['Privilege'] = GetGroupPrivilege($group_id);
}

class groupclass {
	var $group_id;
	var $group_name;
	var $privilege;

	function setgroupid($id) {
		$this->group_id = $id;
	}
	function setgroupname($name) {
		$this->group_name = $name;
	}
	function setprivilege($privilege) {
		$this->privilege = $privilege;
	}
	function getgroupid() {
		return $this->group_id;
	}
	function getgroupname() {
		return $this->group_name;
	}
	function getprivilege() {
		return $this->privilege;
	}
}

function GetAllGroups()
{
	$all_group_sql = "select * from ".$GLOBALS['BR_group_table']." order by group_name DESC";
	$all_group_result = $GLOBALS['connection']->Execute($all_group_sql) or DBError(__FILE__.":".__LINE__);
	$group_array = array();
	while ($row = $all_group_result->FetchRow()) {
		$group_id = $row["group_id"];
		$group_name = $row["group_name"];
		$privilege = $row["privilege"];
		
		$new_group = new groupclass;
		$new_group->setgroupid($group_id);
		$new_group->setgroupname($group_name);
		$new_group->setprivilege($privilege);
		
		array_unshift($group_array, $new_group);
	}
    $all_group_result->Free();

	return $group_array;
}

function GidToGroupName($group_array, $gid) {
	if (!is_numeric($gid)) {return;}
	for($i=0; $i<sizeof($group_array); $i++) {
		if ($gid == $group_array[$i]->getgroupid()) {
			if ($group_array[$i]->getgroupname() != "") {
				return $group_array[$i]->getgroupname();
			} else {
				return "";
			}
		}
	}
	// 沒有在陣列中找到
	return "";
}
?>
