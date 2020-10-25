<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document_doedit.php,v 1.12 2013/06/29 11:33:40 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

function CopyOldFileToHistory($old_filename, $preserve)
{
	if (!file_exists("documents/history")) {
		mkdir("documents/history");
	}
	$new_old_filename = $old_filename;
	$dest_file = "documents/history/".$old_filename;
	$num=1;
	while (file_exists($dest_file)) {
		if ($num > 100) {
			$subname = strrchr($old_filename, ".");
			if (utf8_strlen($subname) > 250) {
				$new_old_filename = date("U");
				$dest_file = "documents/history/".$new_old_filename;
			} else {
				$new_old_filename = date("U").$subname;
				$dest_file = "documents/history/".$new_old_filename;
			}				
		} else {
			$new_old_filename = $num."_".$old_filename;
			$dest_file = "documents/history/".$new_old_filename;
		}
		$num++; // if previous file name existed then thy another number+_+filename
	}
	if ($preserve) {
		copy("documents/".$old_filename, $dest_file);
	} else {
		rename("documents/".$old_filename, $dest_file);
	}
	

	return $new_old_filename;
}

if (!$_POST['document_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "document_id");
}
$sql = "select * from ".$GLOBALS['BR_document_table']." 
		where document_id=".$GLOBALS['connection']->QMagic($_POST['document_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$line = $result->Recordcount();
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "document", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "document");
}
$created_by = $result->fields["created_by"];
$old_filename = $result->fields["filename"];

if (!($GLOBALS['Privilege'] & $GLOBALS['can_update_document']) && 
	($created_by != $_SESSION[SESSION_PREFIX.'uid'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
$_POST['subject'] = trim($_POST['subject']);
if ($_POST['subject'] == "") {
	ErrorPrintBackFormOut("GET", "document_edit.php", 
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
		ErrorPrintBackFormOut("GET", "document_edit.php", $_POST, 
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
		if ($old_filename != "") {
			$new_old_filename = CopyOldFileToHistory($old_filename, false);
		}
		$org_filename = $filename;
		$dest_file = "documents/".$filename;

		$num = 1;
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
	}
}

$_POST['subject'] = htmlspecialchars($_POST['subject']);

$now = $GLOBALS['connection']->DBTimeStamp(time());
if (($filename == "") && ($_POST['delete_old_file'] == "Y")){
	if ($GLOBALS['SYS_FILE_IN_DB'] != 1) {
		if ($old_filename != "") {
			$new_old_filename = CopyOldFileToHistory($old_filename, false);
		}
	}

	$sql = "update ".$GLOBALS['BR_document_table']." set
			subject=".$GLOBALS['connection']->QMagic($_POST['subject']).", group_class=".$GLOBALS['connection']->QMagic($_POST['group_class']).",
			allow_other_group=".$GLOBALS['connection']->QMagic($_POST['allow_other_group']).",
			description=".$GLOBALS['connection']->QMagic($_POST['description']).",
			created_by=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).",
			last_update=$now, filename=".$GLOBALS['connection']->QMagic("").", filedata=".$GLOBALS['connection']->QMagic("")." 
			where document_id=".$GLOBALS['connection']->QMagic($_POST['document_id']);
} elseif ($filename == "") {
	if ($GLOBALS['SYS_FILE_IN_DB'] != 1) {
		if ($old_filename != "") {
			$new_old_filename = CopyOldFileToHistory($old_filename, true);
		}
	}
	$sql = "update ".$GLOBALS['BR_document_table']." set
			subject=".$GLOBALS['connection']->QMagic($_POST['subject']).", group_class=".$GLOBALS['connection']->QMagic($_POST['group_class']).",
			allow_other_group=".$GLOBALS['connection']->QMagic($_POST['allow_other_group']).",
			description=".$GLOBALS['connection']->QMagic($_POST['description']).",
			created_by=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).",
			last_update=$now
			where document_id=".$GLOBALS['connection']->QMagic($_POST['document_id']);

} else {
	$sql = "update ".$GLOBALS['BR_document_table']." set
			subject=".$GLOBALS['connection']->QMagic($_POST['subject']).", group_class=".$GLOBALS['connection']->QMagic($_POST['group_class']).",
			allow_other_group=".$GLOBALS['connection']->QMagic($_POST['allow_other_group']).",
			description=".$GLOBALS['connection']->QMagic($_POST['description']).",
			created_by=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).",
			last_update=$now, filename=".$GLOBALS['connection']->QMagic($filename).", filedata=".$GLOBALS['connection']->QMagic($filedata)." 
			where document_id='".$_POST['document_id']."'";
}

$history_sql = "INSERT INTO ".$GLOBALS['BR_document_history_table']."(
				document_id, subject, created_by, created_date, description, filename, filedata)
				SELECT document_id,
				subject, created_by, last_update as created_date, description, filename, filedata
                FROM ".$GLOBALS['BR_document_table']." WHERE document_id=".$GLOBALS['connection']->QMagic($_POST['document_id']);

$GLOBALS['connection']->StartTrans();

$GLOBALS['connection']->Execute($history_sql) or DBError(__FILE__.":".__LINE__);
$document_history_id = $GLOBALS['connection']->Insert_ID($GLOBALS['BR_document_history_table'], 'document_history_id');

$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

if (($GLOBALS['SYS_FILE_IN_DB'] != 1) && isset($new_old_filename) && $new_old_filename != $old_filename) {
	$sql = "UPDATE ".$GLOBALS['BR_document_history_table']." SET filename=".$GLOBALS['connection']->QMagic($new_old_filename)." WHERE
			document_history_id=".$document_history_id;
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
}

// Remove old category
$sql = "delete from ".$GLOBALS['BR_document_map_table']." where document_id=".$GLOBALS['connection']->QMagic($_POST['document_id']);
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$all_belong_class = explode(",", $_POST['belong_class']);

for ($i=0; $i < sizeof($all_belong_class); $i++) {
	if (!is_numeric($all_belong_class[$i])) {
		continue;
	}
	$sql = "insert into ".$GLOBALS['BR_document_map_table']."(document_id, document_class_id) 
			values(".$GLOBALS['connection']->QMagic($_POST['document_id']).", ".$GLOBALS['connection']->QMagic($all_belong_class[$i]).")";
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
}

$GLOBALS['connection']->CompleteTrans();

FinishPrintOut("document.php", "finish_update", "document");

include("../include/tail.php");
?>
