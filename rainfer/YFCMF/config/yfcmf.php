<?php
return array (
  'yfcmf_version' => 'V3.0.0',
  'verify' => 
  array (
    'fontSize' => 20,
    'fontttf' => '4.ttf',
    'imageH' => 42,
    'imageW' => 250,
    'length' => 5,
    'useCurve' => false,
  ),
  'adminpath' => 'manage',
  'app_trace' => true,
  'app_debug' => true,
  'baidumap_ak' => 'D91c810554767b49e3bdd2a7b25d97c1',
  'upload_path' => '/data/upload',
  'log' => 
  array (
    'clear_on' => true,
    'timebf' => 50,
    'level' => 
    array (
    ),
  ),
  'web_log' => 
  array (
    'weblog_on' => false,
    'not_log_module' => 
    array (
      0 => 'install',
    ),
    'not_log_controller' => 
    array (
      0 => 'home/Error',
      1 => 'home/Token',
      2 => 'admin/Ajax',
      3 => 'admin/Error',
      4 => 'admin/Ueditor',
      5 => 'admin/WebLog',
    ),
    'not_log_action' => 
    array (
    ),
    'not_log_data' => 
    array (
    ),
    'not_log_request_method' => 
    array (
      0 => 'GET',
    ),
  ),
  'lang_switch_on' => true,
  'default_lang' => 'zh-cn',
  'url_route_on' => true,
  'url_route_must' => false,
  'route_complete_match' => false,
  'url_html_suffix' => 'html',
  'url_route_mode' => '1',
  'comment' => 
  array (
    't_open' => true,
    't_limit' => 60,
  ),
  'url_domain_deploy' => true,
  'cache' => 
  array (
  ),
  'api_addon' => 
  array (
    'url' => 'http://api.yfcmf.net'
  ),
);