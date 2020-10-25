<?php

function smarty_modifier_count_sentences($string)
{
return preg_match_all('/[^\s]\.(?!\w)/',$string,$match);
}

?>