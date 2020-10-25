<?php

function smarty_modifier_wordwrap($string,$length=80,$break="\n",$cut=false)
{
return wordwrap($string,$length,$break,$cut);
}

?>