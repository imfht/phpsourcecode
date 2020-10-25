<?php

function smarty_function_escape_special_chars($string)
{
if(!is_array($string)) {
$string = preg_replace('!&(#?\w+);!','%%%SMARTY_START%%%\\1%%%SMARTY_END%%%',$string);
$string = htmlspecialchars($string);
$string = str_replace(array('%%%SMARTY_START%%%','%%%SMARTY_END%%%'),array('&',';'),$string);
}
return $string;
}
?>