<?php

function smarty_outputfilter_trimwhitespace($source,&$smarty)
{
preg_match_all("!<script[^>]+>.*?</script>!is",$source,$match);
$_script_blocks = $match[0];
$source = preg_replace("!<script[^>]+>.*?</script>!is",
'@@@SMARTY:TRIM:SCRIPT@@@',$source);
preg_match_all("!<pre>.*?</pre>!is",$source,$match);
$_pre_blocks = $match[0];
$source = preg_replace("!<pre>.*?</pre>!is",
'@@@SMARTY:TRIM:PRE@@@',$source);
preg_match_all("!<textarea[^>]+>.*?</textarea>!is",$source,$match);
$_textarea_blocks = $match[0];
$source = preg_replace("!<textarea[^>]+>.*?</textarea>!is",
'@@@SMARTY:TRIM:TEXTAREA@@@',$source);
$source = trim(preg_replace('/((?<!\?>)\n)[\s]+/m','\1',$source));
smarty_outputfilter_trimwhitespace_replace("@@@SMARTY:TRIM:TEXTAREA@@@",$_textarea_blocks,$source);
smarty_outputfilter_trimwhitespace_replace("@@@SMARTY:TRIM:PRE@@@",$_pre_blocks,$source);
smarty_outputfilter_trimwhitespace_replace("@@@SMARTY:TRIM:SCRIPT@@@",$_script_blocks,$source);
return $source;
}
function smarty_outputfilter_trimwhitespace_replace($search_str,$replace,&$subject) {
$_len = strlen($search_str);
$_pos = 0;
for ($_i=0,$_count=count($replace);$_i<$_count;$_i++)
if (($_pos=strpos($subject,$search_str,$_pos))!==false)
$subject = substr_replace($subject,$replace[$_i],$_pos,$_len);
else
break;
}

?>