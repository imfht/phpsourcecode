<?php

function smarty_core_write_cache_file($params,&$smarty)
{
$smarty->_cache_info['timestamp'] = time();
if ($smarty->cache_lifetime >-1){
$smarty->_cache_info['expires'] = $smarty->_cache_info['timestamp'] +$smarty->cache_lifetime;
}else {
$smarty->_cache_info['expires'] = -1;
}
if (preg_match_all('!\{(/?)nocache\:[0-9a-f]{32}#\d+\}!',$params['results'],$match,PREG_PATTERN_ORDER)) {
$match_count = count($match[0]);
$results = preg_split('!(\{/?nocache\:[0-9a-f]{32}#\d+\})!',$params['results'],-1,PREG_SPLIT_DELIM_CAPTURE);
$level = 0;
$j = 0;
for ($i=0,$results_count = count($results);$i <$results_count &&$j <$match_count;$i++) {
if ($results[$i] == $match[0][$j]) {
if ($match[1][$j]) {
$level--;
unset($results[$i]);
}else {
if ($level++>0) unset($results[$i]);
}
$j++;
}elseif ($level >0) {
unset($results[$i]);
}
}
$params['results'] = implode('',$results);
}
$smarty->_cache_info['cache_serials'] = $smarty->_cache_serials;
$_cache_info = serialize($smarty->_cache_info);
$params['results'] = strlen($_cache_info) ."\n".$_cache_info .$params['results'];
if (!empty($smarty->cache_handler_func)) {
call_user_func_array($smarty->cache_handler_func,
array('write',&$smarty,&$params['results'],$params['tpl_file'],$params['cache_id'],$params['compile_id'],null));
}else {
if(!@is_writable($smarty->cache_dir)) {
if(!@is_dir($smarty->cache_dir)) {
$smarty->trigger_error('the $cache_dir \''.$smarty->cache_dir .'\' does not exist, or is not a directory.',E_USER_ERROR);
return false;
}
$smarty->trigger_error('unable to write to $cache_dir \''.realpath($smarty->cache_dir) .'\'. Be sure $cache_dir is writable by the web server user.',E_USER_ERROR);
return false;
}
$_auto_id = $smarty->_get_auto_id($params['cache_id'],$params['compile_id']);
$_cache_file = $smarty->_get_auto_filename($smarty->cache_dir,$params['tpl_file'],$_auto_id);
$_params = array('filename'=>$_cache_file,'contents'=>$params['results'],'create_dirs'=>true);
require_once(SMARTY_CORE_DIR .'core.write_file.php');
smarty_core_write_file($_params,$smarty);
return true;
}
}
?>