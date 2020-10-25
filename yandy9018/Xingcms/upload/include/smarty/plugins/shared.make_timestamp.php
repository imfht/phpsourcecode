<?php

function smarty_make_timestamp($string)
{
if(empty($string)) {
$time = time();
}elseif (preg_match('/^\d{14}$/',$string)) {
$time = mktime(substr($string,8,2),substr($string,10,2),substr($string,12,2),
substr($string,4,2),substr($string,6,2),substr($string,0,4));
}elseif (is_numeric($string)) {
$time = (int)$string;
}else {
$time = strtotime($string);
if ($time == -1 ||$time === false) {
$time = time();
}
}
return $time;
}

?>