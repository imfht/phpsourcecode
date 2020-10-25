<?php

function smarty_block_textformat($params,$content,&$smarty)
{
if (is_null($content)) {
return;
}
$style = null;
$indent = 0;
$indent_first = 0;
$indent_char = ' ';
$wrap = 80;
$wrap_char = "\n";
$wrap_cut = false;
$assign = null;
foreach ($params as $_key =>$_val) {
switch ($_key) {
case 'style':
case 'indent_char':
case 'wrap_char':
case 'assign':
$$_key = (string)$_val;
break;
case 'indent':
case 'indent_first':
case 'wrap':
$$_key = (int)$_val;
break;
case 'wrap_cut':
$$_key = (bool)$_val;
break;
default:
$smarty->trigger_error("textformat: unknown attribute '$_key'");
}
}
if ($style == 'email') {
$wrap = 72;
}
$_paragraphs = preg_split('![\r\n][\r\n]!',$content);
$_output = '';
for($_x = 0,$_y = count($_paragraphs);$_x <$_y;$_x++) {
if ($_paragraphs[$_x] == '') {
continue;
}
$_paragraphs[$_x] = preg_replace(array('!\s+!','!(^\s+)|(\s+$)!'),array(' ',''),$_paragraphs[$_x]);
if($indent_first >0) {
$_paragraphs[$_x] = str_repeat($indent_char,$indent_first) .$_paragraphs[$_x];
}
$_paragraphs[$_x] = wordwrap($_paragraphs[$_x],$wrap -$indent,$wrap_char,$wrap_cut);
if($indent >0) {
$_paragraphs[$_x] = preg_replace('!^!m',str_repeat($indent_char,$indent),$_paragraphs[$_x]);
}
}
$_output = implode($wrap_char .$wrap_char,$_paragraphs);
return $assign ?$smarty->assign($assign,$_output) : $_output;
}

?>