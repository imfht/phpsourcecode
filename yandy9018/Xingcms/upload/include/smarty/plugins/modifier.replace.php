<?php

function smarty_modifier_replace($string,$search,$replace)
{
return str_replace($search,$replace,$string);
}

?>