<?php

function smarty_modifier_count_paragraphs($string)
{
return count(preg_split('/[\r\n]+/',$string));
}

?>