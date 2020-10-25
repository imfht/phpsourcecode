<?php

function smarty_modifier_default($string,$default = '')
{
if (!isset($string) ||$string === '')
return $default;
else
return $string;
}

?>