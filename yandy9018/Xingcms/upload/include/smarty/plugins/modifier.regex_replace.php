<?php

function smarty_modifier_regex_replace($string,$search,$replace)
{
if (preg_match('!([a-zA-Z\s]+)$!s',$search,$match) &&(strpos($match[1],'e') !== false)) {
$search = substr($search,0,-strlen($match[1])) .preg_replace('![e\s]+!','',$match[1]);
}
return preg_replace($search,$replace,$string);
}

?>