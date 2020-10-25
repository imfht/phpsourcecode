<?php

require '_hicms/vendor/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

require '_hicms/_hicms.php';
require '_hicms/vendor/Lex/Autoloader.php';
require '_hicms/vendor/Yaml/spyc.php';

$config = HiCMS::load_all_configs();

$public_path = isset($config['_public_path']) ? $config['_public_path'] : '';

$config['theme_path'] = '_themes/'.$config['_theme']."/";
$config['templates.path'] = HiCMS_helper::reduce_double_slashes($public_path.'_themes/'.$config['_theme']."/");
$config['debug'] = $config['_debug'];
$config['view'] = new HiCMS_View();

$app = new \Slim\Slim($config);
$app->config = $config;

if (HiCMS::get_setting('_content_type', false) == 'markdown_edge') {

  require '_hicms/vendor/Markup/markdown_edge.php';
} else {
  require '_hicms/vendor/Markup/markdown.php';
}

require '_hicms/vendor/Markup/smartypants.php';
require '_hicms/vendor/Markup/classTextile.php';

HiCMS_View::set_layout("layouts/default");
require '_hicms/_hicms_routes.php';

$app->run();