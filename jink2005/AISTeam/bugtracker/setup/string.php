<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: string.php,v 1.16 2013/10/12 15:25:23 alex Exp $
 *
 */
include_once("../include/db.php");
include_once("../include/misc.php");

/* To add your own string file: */
$lang_array = array("ar" => "Arabic",
					"cht" => "Chinese Traditional", 
					"chs" => "Chinese Simplified", 
					"cs" => "Czech",
					"da" => "Danish",
					"de" => "German",
					"en" => "English", 
					"es" => "Spanish", 
					"fi" => "Finnish",
					"fr" => "French", 
					"gr" => "Greek",
					"he" => "Hebrew",
					"is" => "Icelandic",
					"it" => "Italian",
					"ja" => "Japanese", 
					"ko" => "Korean",
					"nl" => "Dutch",
					"no" => "Norwegian",
					"pl" => "Polish", 
					"pt" => "Portuguese",
					"ru" => "Russian", 
					"sv" => "Swedish",
					"sk" => "Slovak",
					"th" => "Thai",
					"tr" => "Turkish",
					"uk" => "Ukrainian",
					);
$lang = array_keys($lang_array);

$GLOBALS['connection']->StartTrans();

// Remove old strings
echo "Remove all strings in string table....<br>";
$GLOBALS['connection']->Execute("delete from ".$GLOBALS['BR_string_table']);
$GLOBALS['connection']->Execute("delete from ".$GLOBALS['BR_language_table']);

$count = 1;
$error_mesg = "";
$lang_count = 0;
$supported_lang = "";
$check_array = array();
for ($i=0; $i<sizeof($lang); $i++) {
	
	if (!file_exists("strings/string.".$lang[$i])) {
		continue;
	} else {
		$supported_lang .= $lang_array[$lang[$i]].", ";
		$lang_count++;
	}
	$fp = fopen("strings/string.".$lang[$i], "r");
	if ($fp == FALSE) {
		$error_mesg = "<li>Failed to open file strings/string.".$lang[$i]."</li>";
		break;
	}
	$section = "common";

	$sql = "INSERT INTO ".$GLOBALS['BR_language_table']."(language, language_desc) 
			VALUES(".$GLOBALS['connection']->QMagic($lang[$i]).", ".$GLOBALS['connection']->QMagic($lang_array[$lang[$i]]).")";
	$GLOBALS['connection']->Execute($sql);

	$first_line = 1;
	while (!feof($fp)) {
		$buf = fgets($fp);
		$buf = trim($buf);
		if ($first_line) {
			if (utf8_ord($buf{0}) == 53120) { // UTF8 file header
				$buf = utf8_substr($buf, 1);
			}
			$first_line = 0;
		}
		if (($buf == "") || (substr($buf, 0, 1) == "#")) {
			continue;
		}

		if ((substr($buf, 0, 1) == "[") && (substr($buf, -1, 1) == "]")){
			$section = substr($buf, 1, -1);
			continue;
		}

		list($key, $value) = explode("=", $buf);
		$key = trim($key);
		$value = trim($value);
		if (substr($value, 0, 1) == "\"") {
			$value = substr($value, 1);
		}
		if (substr($value, -1, 1) == "\"") {
			$value = substr($value, 0, -1);
		}
		
		$check_array[$key]++;
		$sql = "insert into ".$GLOBALS['BR_string_table']."(string_id, string_section, string_key, string_value, string_lang)
		values($count, 
			".$GLOBALS['connection']->qstr($section).", 
			".$GLOBALS['connection']->qstr($key).", 
			".$GLOBALS['connection']->qstr($value).", 
			".$GLOBALS['connection']->qstr($lang[$i]).")";
		
		$GLOBALS['connection']->Execute($sql) or die($GLOBALS['connection']->ErrorMsg());
		$count++;

	}
	fclose($fp);
	
}

$check_key = array_keys($check_array);
for ($i = 0; $i < sizeof($check_key); $i++) {
	if ($check_array[$check_key[$i]] != $lang_count) {
		$error_mesg .="<li>Miss language key: ".$check_key[$i].". It only appears in ".$check_array[$check_key[$i]]." language file.</li>";
	}
}
$GLOBALS['connection']->CompleteTrans();

if ($error_mesg == "") {
	echo "<p>Finish string setup!!</p>";
	echo "<p>Supported language: ".$supported_lang."</p>";
} else {
	echo "<p>Supported language: ".$supported_lang."</p>";
	echo "<p>Failed to create strings:</p>";
	echo "<ul>";
	echo $error_mesg;
	echo "</ul>";
}

?>



