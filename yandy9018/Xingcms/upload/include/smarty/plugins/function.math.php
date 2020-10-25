<?php

function smarty_function_math($params,&$smarty)
{
if (empty($params['equation'])) {
$smarty->trigger_error("math: missing equation parameter");
return;
}
$equation = $params['equation'];
if (substr_count($equation,"(") != substr_count($equation,")")) {
$smarty->trigger_error("math: unbalanced parenthesis");
return;
}
preg_match_all("!(?:0x[a-fA-F0-9]+)|([a-zA-Z][a-zA-Z0-9_]+)!",$equation,$match);
$allowed_funcs = array('int','abs','ceil','cos','exp','floor','log','log10',
'max','min','pi','pow','rand','round','sin','sqrt','srand','tan');
foreach($match[1] as $curr_var) {
if ($curr_var &&!in_array($curr_var,array_keys($params)) &&!in_array($curr_var,$allowed_funcs)) {
$smarty->trigger_error("math: function call $curr_var not allowed");
return;
}
}
foreach($params as $key =>$val) {
if ($key != "equation"&&$key != "format"&&$key != "assign") {
if (strlen($val)==0) {
$smarty->trigger_error("math: parameter $key is empty");
return;
}
if (!is_numeric($val)) {
$smarty->trigger_error("math: parameter $key: is not numeric");
return;
}
$equation = preg_replace("/\b$key\b/"," \$params['$key'] ",$equation);
}
}
eval("\$smarty_math_result = ".$equation.";");
if (empty($params['format'])) {
if (empty($params['assign'])) {
return $smarty_math_result;
}else {
$smarty->assign($params['assign'],$smarty_math_result);
}
}else {
if (empty($params['assign'])){
printf($params['format'],$smarty_math_result);
}else {
$smarty->assign($params['assign'],sprintf($params['format'],$smarty_math_result));
}
}
}
?>