<?php

function smarty_modifier_strip_tags($string,$replace_with_space = true)
{
if ($replace_with_space)
return preg_replace('!<[^>]*?>!',' ',$string);
else
return strip_tags($string);
}

?>