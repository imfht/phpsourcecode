<?php

function smarty_modifier_count_characters($string,$include_spaces = false)
{
if ($include_spaces)
return(strlen($string));
return preg_match_all("/[^\s]/",$string,$match);
}

?>