<?php


/////////////////////////////////////////////////////////////////////////////////////////////////
// USER & SESSION AUTH
/////////////////////////////////////////////////////////////////////////////////////////////////

$current_user = HiCMS_Auth::get_current_user();
$app->config['logged_in'] = $current_user !== false;
$app->config['username'] = $current_user ? $current_user->get_name() : false;
$app->config['is_admin'] = $current_user ? $current_user->has_role('admin') : false;
$app->config['user'] = $current_user ? array(
  array('first_name' => $current_user->get_first_name()),
  array('last_name' => $current_user->get_last_name()),
  array('bio' => $current_user->get_biography())
) : false;


/////////////////////////////////////////////////////////////////////////////////////////////////
// ENVIRONMENTS
/////////////////////////////////////////////////////////////////////////////////////////////////

$environments = HiCMS::get_setting('_environments');

if (is_array($environments)) {
  $environment = HiCMS::detect_environment($environments, $app->request()->getUrl());
  if ($environment) {
    $app->config['environment'] = $environment;
    $app->config['is_'.$environment] = true;
    $environment_config = Spyc::YAMLLoad(file_get_contents("_config/environments/{$environment}.yaml"));
    $app->config = array_merge($app->config, $environment_config);
  }
}



/////////////////////////////////////////////////////////////////////////////////////////////////
// HOOKS
/////////////////////////////////////////////////////////////////////////////////////////////////


$app->map('/TRIGGER/:add_on/:hook', function($add_on, $hook) use ($app) {

  $locations = array('_hicms/_hicms_plugin/', '_add-ons/');
  
  foreach ($locations as $folder) {

    $file = $folder.'/'.$add_on.'/hooks.'.$add_on.'.php';

    if (is_dir($folder) && is_file($file)) {

      //require_once '_app/hicms/hooks.php';
      require_once($file);
      
      $hook_class = 'Hooks_'.$add_on;

      if (class_exists($hook_class) && method_exists($hook_class, $hook)) {
        $class = new $hook_class();
        $class->$hook();
      }
      
    }
  }

})->via('GET', 'POST', 'HEAD');

/////////////////////////////////////////////////////////////////////////////////////////////////
// CONTENT ROUTING
/////////////////////////////////////////////////////////////////////////////////////////////////


$app->map('/(:segments+)', function ($segments = array()) use ($app) {

  foreach ($segments as $key => $seg) {
    $count = $key+1;
    $app->config['segment_' . $count] = $seg;
  }

  # Ignore segments via routes.yaml
  if (isset($app->config['_routes']['ignore']) && is_array($app->config['_routes']['ignore']) && count($app->config['_routes']['ignore']) > 0) {
    $ignore = $app->config['_routes']['ignore'];

    $remove_segments = array_intersect($ignore, $segments);
    $segments = array_diff($segments, $remove_segments);
  }

  $path = '/'.implode($segments, '/');
  $current_url = $path;

  if (substr($path, -5) == '.html') {
    $path = str_replace('.html', '', $path); # allow mod_rewrite for .html file extensions
  }

  $app->config['current_path'] = $path;


  $content_root = HiCMS::get_content_root();
  $content_type = HiCMS::get_content_type();
  $response_code = 200;
  $visible = true;
  $add_prev_next = false;

  $template_list = array('default');

  if (file_exists("{$content_root}/{$path}.{$content_type}") || is_dir("{$content_root}/{$path}")) {
    // endpoint or folder exists!
  } else {

    $path = HiCMS_Helper::resolve_path($path);
    $app->config['current_url'] = $app->config['current_path'];
    $app->config['current_path'] = $path; # override global current_path
  }

  # Routes via routes.yaml
  if (isset($app->config['_routes']['routes'][$current_url]) || isset($app->config['_routes'][$current_url])) {

    # allows the route file to run without "route:" as the top level array key (backwards compatibility)
    $current_route = isset($app->config['_routes']['routes'][$current_url]) ? $app->config['_routes']['routes'][$current_url] : $app->config['_routes'][$current_url];

    $route    = $current_route;
    $template = $route;
    $data     = array();
    
    if (is_array($route)) {
      $template = isset($route['template']) ? $route['template'] : 'default';
      if (isset($route['layout'])) {
        $data['_layout'] = $route['layout'];
      }
    }

    $template_list = array($template);

  } else if (file_exists("{$content_root}/{$path}.{$content_type}")) {
    $add_prev_next = true;
    $template_list[] = 'post';
    $page     = basename($path);
    $folder   = substr($path, 0, (-1*strlen($page))-1);

    $data = HiCMS::get_content_meta($page, $folder);
    $data['current_url'] = $current_url;
    $data['slug'] = basename($current_url);

  } else if (HiCMS::is_taxonomy_url($path)) {
    list($type, $slug) = HiCMS::get_taxonomy_criteria($path);
    $data = HiCMS::get_content_meta($type, HiCMS::remove_taxonomy_from_path($path, $type, $slug));
    $data['taxonomy_slug'] = urldecode($slug);

    $template_list[] = "taxonomies";
    $template_list[] = $type;

  } else if (is_dir("{$content_root}/{$path}")) {
    $data = HiCMS::get_content_meta("page", $path);

  } else {
    $data = HiCMS::get_content_meta("404", "/");
    $template_list = array('404');
    $response_code = 404;
  }

  # We now have all the YAML content
  # Let's process action fields

  # Redirect
  if (isset($data['_redirect'])) {
    $response = 302;

    if (is_array($data['_redirect'])) {
      $url = isset($data['_redirect']['to']) ? $data['_redirect']['to'] : false;
      
      if (! $url) {
        $url = isset($data['_redirect']['url']) ? $data['_redirect']['url'] : false; #support url key as alt
      }

      $response = isset($data['_redirect']['response']) ? $data['_redirect']['response'] : $response;
    } else {
      $url = $data['_redirect'];
    }

    if ($url) {
      $app->redirect($url, $response);
    }
  }

  if (isset($data['status'])) {
    if ($data['status'] != 'live' && $data['status'] != 'hidden' && ! $app->config['logged_in']) {
      $data = HiCMS::get_content_meta("404", "/");
      $template_list = array('404');
      $visible = false;
      $response_code = 404;
    }
  }

  if ($add_prev_next && $visible) {
    $prev = HiCMS::find_prev($page, $folder);
    $next = HiCMS::find_next($page, $folder);

    if ($prev) {
      $prev = HiCMS_Helper::remove_numerics_from_path($folder."/".$prev);
      $prev = HiCMS_Helper::reduce_double_slashes($prev);
    }

    if ($next) {
      $next = HiCMS_Helper::remove_numerics_from_path($folder."/".$next);
      $next = HiCMS_Helper::reduce_double_slashes($next);
    }

    $data['prev'] = $prev;
    $data['next'] = $next;

    $folder_data = HiCMS::get_content_meta("page", dirname($path));
    if ( ! isset($data['_template']) && isset($folder_data['_default_folder_template'])) {
      $data['_template'] = $folder_data['_default_folder_template'];
    }
  }

  //return $key[explode(":", $tag)];

  # Set template
  if (isset($data['_template'])){
    $template_list[] = $data['_template'];
  }

  # Set layout
  if (isset($data['_layout'])) {
    HiCMS_View::set_layout("layouts/{$data['_layout']}");
  }

  HiCMS_View::set_templates(array_reverse($template_list));

  # Set type
  if (isset($data['_type'])) {
    if ($data['_type'] == 'rss') {
      $data['_xml_header'] = '<?xml version="1.0" encoding="utf-8"?>';
      $response = $app->response();
      $response['Content-Type'] = 'application/xml';
    }
  }
  $app->render(null, $data, $response_code);
  $app->halt($response_code, ob_get_clean());
})->via('GET', 'POST', 'HEAD');