<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: string_function.php,v 1.4 2013/07/07 21:31:13 alex Exp $
 *
 */
function GetDefaultLang()
{
	$browser_lang = explode(",", strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));

	$supported_lang = array();
	$get_language_sql = "select * from ".$GLOBALS['BR_language_table']." order by language_desc";
	$get_language_result = $GLOBALS['connection']->Execute($get_language_sql) or DBError(__FILE__.":".__LINE__);
	while ($lang_row = $get_language_result->FetchRow()) {
		$language = $lang_row["language"];
		array_push($supported_lang, $language);
	}

	$return_lang = "";
	for ($i = 0; $i < sizeof($browser_lang); $i++) {
		list($lang, $dummy) = explode(";", $browser_lang[$i], 2);
		if (($lang == "zh-tw") || ($lang == "zh-sg") || ($lang == "zh-hk") || strstr($lang,"zh-hant")) {
			if (-1 == IsInArray($supported_lang, "cht")) {
				continue;
			} else {
				$return_lang = "cht";
			}
		} else if (($lang == "zh") || (strncmp($lang, "zh-", 3) == 0)) {
			if (-1 == IsInArray($supported_lang, "chs")) {
				continue;
			} else {
				$return_lang = "chs";
			}
		} else {
			if (-1 != IsInArray($supported_lang, $lang)) {
				$return_lang = $lang;
			} else if (strstr($lang, "-")) {
				$lang = substr($lang, 0, 2);
				if (-1 != IsInArray($supported_lang, $lang)) {
					$return_lang = $lang;
				}
			}
		}

		if ($return_lang != "") {
			break;
		}
	}
	if ($return_lang == "") {
		return "en";
	} else {
		return $return_lang;
	}
}

function GetLanguageSetting()
{
	if (!isset($_SESSION[SESSION_PREFIX.'uid'])) {
		$language = GetDefaultLang();
	} else {
		$user_id = $_SESSION[SESSION_PREFIX.'uid'];
		$get_lang_sql = "select language from ".$GLOBALS['BR_user_table']." where user_id=".$user_id;
		$get_lang_result = $GLOBALS['connection']->Execute($get_lang_sql) or DBError(__FILE__.":".__LINE__);
		if ($get_lang_result->RecordCount() == 0) {
			$language = GetDefaultLang();
		} else {
			$language = $get_lang_result->fields["language"];
			if ($language == "enu") {
				$language = "en";
			}
			if ($language == "") {
				$language = GetDefaultLang();
			}
		}
	}
	return $language;
}

function GetStringArray($language, $excape_html)
{
	$string_sql = "select * from ".$GLOBALS['BR_string_table']." where string_lang=".$GLOBALS['connection']->QMagic($language);
	$string_result = $GLOBALS['connection']->Execute($string_sql) or DBError(__FILE__.":".__LINE__);

	$S = array();
	while ($string_row = $string_result->FetchRow()) {
		$string_key = $string_row["string_key"];
		$string_value = $string_row["string_value"];
		if ($excape_html) {
			$string_value = htmlspecialchars($string_value);
		}
		$S[$string_key] = $string_value;
	}
	return $S;
}

if (!isset($_SESSION[SESSION_PREFIX.'language'])) {
	$_SESSION[SESSION_PREFIX.'language'] = GetLanguageSetting();
}
	
$STRING = GetStringArray($_SESSION[SESSION_PREFIX.'language'], true);
?>
