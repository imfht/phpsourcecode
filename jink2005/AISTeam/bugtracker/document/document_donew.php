<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document_donew.php,v 1.10 2013/06/29 11:33:40 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_create_document'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

$_POST['subject'] = trim($_POST['subject']);
if ($_POST['subject'] == "") {
	ErrorPrintBackFormOut("GET", "document_new.php", 
						  $_POST, "no_empty", "subject");
}
if ($_POST['allow_other_group'] == "Y") {
	$_POST['allow_other_group'] = "t";
} else {
	$_POST['allow_other_group'] = "f";
}
if ($_POST['group_class'] == "-1") {
	$_POST['allow_other_group'] = "t";
}

// 上傳附加檔案資料
if(!$_FILES['file']['tmp_name']) {
	$filename = "";
	if ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
		ErrorPrintBackFormOut("GET", "document_new.php", $_POST, 
							  "exceed_max_size", "", ini_get("upload_max_filesize"));
	}
} else {
	if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
		ErrorPrintBackFormOut("GET", "document_new.php", $_POST, 
							  "wrong_format", "file_upload");
	}

	$org_filename = $_FILES['file']['name'];
	if (utf8_strlen($org_filename) > 252) { /* 100_filename  256-strlen("100_")=252 */
		$subname = strrchr($org_filename, ".");
		if (utf8_strlen($subname) > 251) {
			$filename = utf8_substr($org_filename, 0, 251);
		} else {
			$filename = utf8_substr($org_filename, 0, (251 - utf8_strlen($subname)) ).$subname;
		}
	} else {
		$filename = $org_filename;
	}
	
	if ($GLOBALS['SYS_FILE_IN_DB'] == 1) {
		$filedata = $GLOBALS['connection']->BlobEncode(fread(fopen($_FILES['file']['tmp_name'], "r"), $_FILES['file']['size']));
	} else {
		$org_filename = $filename;
		$dest_file = "documents/".$filename;
		
		$num=1;
		while (file_exists($dest_file)) {
			if ($num > 100) {
				$subname = strrchr($filename, ".");
				if (utf8_strlen($subname) > 250) {
					$filename = date("U");
					$dest_file = "documents/".$filename;
				} else {
					$filename = date("U").$subname;
					$dest_file = "documents/".$filename;
				}				
			} else {
				$filename = $num."_".$org_filename;
				$dest_file = "documents/".$filename;
			}
			$num++; // if previous file name existed then thy another number+_+filename
		}
		move_uploaded_file($_FILES['file']['tmp_name'], $dest_file );
	} // End of file in db
}

$_POST['subject'] = htmlspecialchars($_POST['subject']);

$GLOBALS['connection']->StartTrans();

$now = $GLOBALS['connection']->DBTimeStamp(time());
$sql = "insert into ".$GLOBALS['BR_document_table']."(subject, created_by,
		last_update, description, filename, filedata, group_class, allow_other_group) 
		values(".$GLOBALS['connection']->QMagic($_POST['subject']).", ".$_SESSION[SESSION_PREFIX.'uid'].", $now, 
		".$GLOBALS['connection']->QMagic($_POST['description']).", ".$GLOBALS['connection']->QMagic($filename).", 
		".$GLOBALS['connection']->QMagic($filedata).", 
		".$GLOBALS['connection']->QMagic($_POST['group_class']).", 
		".$GLOBALS['connection']->QMagic($_POST['allow_other_group']).")";

$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$document_id = $GLOBALS['connection']->Insert_ID($GLOBALS['BR_document_table'], 'document_id');

$all_belong_class = explode(",", $_POST['belong_class']);

for ($i=0; $i < sizeof($all_belong_class); $i++) {
	if (!is_numeric($all_belong_class[$i])) {
		continue;
	}
	$sql = "insert into ".$GLOBALS['BR_document_map_table']."(document_id, document_class_id) 
			values(".$GLOBALS['connection']->QMagic($document_id).", ".$GLOBALS['connection']->QMagic($all_belong_class[$i]).")";
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
}

$GLOBALS['connection']->CompleteTrans();

FinishPrintOut("document.php", "finish_new", "document");

include("../include/tail.php");
?>
