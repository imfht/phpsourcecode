<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: user_function.php,v 1.4 2008/11/28 10:36:04 alex Exp $
 *
 */
function UidToUsername ($userarray, $id) {
	if (!is_numeric($id)) {return;}
	for($i=0; $i<sizeof($userarray); $i++) {
		if ($id == $userarray[$i]->getuserid()) {
			if ($userarray[$i]->getusername() != "") {
				return $userarray[$i]->getusername();
			} else {
				return "";
			}
		}
	}
	// 沒有在陣列中找到
	return "";
}

function UidToEmail($userarray, $id) {
	if (!is_numeric($id)) {return;}
	for($i=0; $i<sizeof($userarray); $i++) {
		if ($id == $userarray[$i]->getuserid()) {
			if ($userarray[$i]->getemail() != "") {
				return $userarray[$i]->getemail();
			} else {
				return "";
			}
		}
	}
	// 沒有在陣列中找到
	return "";
}

function IsAccountDisabled($userarray, $id)
{
	if (!is_numeric($id)) {return;}
	for($i=0; $i<sizeof($userarray); $i++) {
		if ($id == $userarray[$i]->getuserid()) {
			if ($userarray[$i]->getdisabled() == 1) {
				return TRUE;
			}else{
				return FALSE;
			}
		}
	}
	return TRUE;
}

class userclass {
	var $userid;
	var $username;
	var $email;
	var $disabled;
	function setuserid($id) {
		$this->userid=$id;
	}
	function setusername($name) {
		$this->username=$name;
	}
	function setemail($address) {
		$this->email=$address;
	}
	function setdisabled($disabled) {
		$this->disabled = $disabled;
	}
	function getuserid() {
		return $this->userid;
	}
	function getusername() {
		return $this->username;
	}
	function getemail() {
		return $this->email;
	}
	function getdisabled() {
		return $this->disabled;
	}
}

/* This function will return all users class.
 * If $with_admin = 1, the admin will then return, too.
 * If $with_disabled_user = 1, disabled user will be return, too.
 */
function GetAllUsers($with_admin, $with_disabled_user)
{

	// 先將所有使用者的資料存入陣列中，以供等一下要將使用者的 id 轉成 username
	// 這樣才不會每一筆資料都要去讀一次資料庫
	$all_user_sql = "select * from ".$GLOBALS['BR_user_table']." order by username DESC";
	$all_user_result = $GLOBALS['connection']->Execute($all_user_sql) or DBError(__FILE__.":".__LINE__);
	$userarray=array();
	while ($all_user_row = $all_user_result->FetchRow()) {
		$user_id=$all_user_row["user_id"];
		$username=$all_user_row["username"];
		$email=$all_user_row["email"];
		$disabled=$all_user_row["account_disabled"];
		
		if ($disabled == 't') {
			$disabled = 1;
		} else {
			$disabled = 0;
		}

		if (($user_id == 0) && ($with_admin == 0)){
			continue;
		}

		if (($disabled == '1') && ($with_disabled_user == 0)){
			continue;
		}
		
		$new_user=new userclass;
		$new_user->setuserid($user_id);
		$new_user->setusername($username);
		$new_user->setemail($email);

		$new_user->setdisabled($disabled);
		array_unshift($userarray,$new_user);
	}
    $all_user_result->Free();

	return $userarray;
}
?>