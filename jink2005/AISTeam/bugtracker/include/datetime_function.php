<?php
$GLOBALS['SYS_DATE_FORMAT'] = array(
	"Y-m-d" => "yyyy-mm-dd",
	"m-d-Y" => "mm-dd-yyyy",
	"d-m-Y" => "dd-mm-yyyy",
	"Y/m/d" => "yyyy/mm/dd",
	"m/d/Y" => "mm/dd/yyyy",
	"d/m/Y" => "dd/mm/yyyy",
	"Y.m.d" => "yyyy.mm.dd",
	"m.d.Y" => "mm.dd.yyyy",
	"d.m.Y" => "dd.mm.yyyy",
	"M d, Y" => " Mmm dd, yyyy",
);

function GetDateFormat()
{
	global $SYSTEM;

	if ($SYSTEM['date_format'] == '') {
		return 'Y-m-d';
	} else {
		return $SYSTEM['date_format'];
	}
}

function GetDateTimeFormat()
{
	return GetDateFormat()." H:i:s";
}

?>
