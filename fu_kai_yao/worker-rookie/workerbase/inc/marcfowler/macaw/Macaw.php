<?php

namespace marcfowler\macaw;

/**
 * @method static Macaw get(string $route, Callable $callback)
 * @method static Macaw post(string $route, Callable $callback)
 * @method static Macaw put(string $route, Callable $callback)
 * @method static Macaw delete(string $route, Callable $callback)
 * @method static Macaw options(string $route, Callable $callback)
 * @method static Macaw head(string $route, Callable $callback)
 */
class Macaw {
  public static $halts = false;
  public static $routes = array();
  public static $methods = array();
  public static $callbacks = array();
  public static $method;
  public static $prefix = '';
  public static $patterns = array(
      ':any' => '(/\S*)*',
      ':num' => '([0-9]+)?',
      ':all' => '.*'
  );
  public static $error_callback;

  //api应用模块命名空间
  public static $web_modules_namespace = 'apps\api\\';

  /**
   * Defines a route w/ callback and method
   */
  public static function __callstatic($method, $params) {
      $filename = dirname($_SERVER['PHP_SELF']);#当前正在执行脚本的文件名
      if (in_array($filename, ['/', DIRECTORY_SEPARATOR, '\\'])) {
          $filename = '';
      }
    $uri = implode('/', array_filter(array(
        $filename,
//        dirname($_SERVER['PHP_SELF']),
      self::$prefix,
      $params[0],
    )));

    $callback = $params[1];

    array_push(self::$routes, $uri);
    array_push(self::$methods, strtoupper($method));
    array_push(self::$callbacks, $callback);
  }

  /**
   * Defines callback if route is not found
  */
  public static function error($callback) {
    self::$error_callback = $callback;
  }

  /**
   * If a function matches, should processing stop?
  */
  public static function haltOnMatch($flag = true) {
    self::$halts = $flag;
  }

  /**
   * What HTTP verb (GET, POST, etc) should we be looking for?
   * If this is not set, it defaults to that in $_SERVER['REQUEST_METHOD']
  */
  public static function setMethod($method) {
    self::$method = strtoupper($method);
  }

  /**
   * Set the prefix to all routes
   * If you know you're running this script at '/some-folder/subfolder/routes', this will strip the first part
  */
  public static function setPrefix($prefix = '') {
    self::$prefix = rtrim(trim($prefix), '/');
  }

  /**
   * Runs the callback for the given request
   */
  public static function dispatch(){
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $method = static::$method;
    if(empty($method)) $method = $_SERVER['REQUEST_METHOD'];

    $searches = array_keys(static::$patterns);
    $replaces = array_values(static::$patterns);

    $found_route = false;

    self::$routes = str_replace('//', '/', self::$routes);

    // Check if route is defined without regex
    if (in_array($uri, self::$routes)) {
      $route_pos = array_keys(self::$routes, $uri);
      foreach ($route_pos as $route) {
        // Using an ANY option to match both GET and POST requests
        if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY') {
          $found_route = true;

          // If route is not an object
          if (!is_object(self::$callbacks[$route])) {

            // Grab all parts based on a / separator
            $parts = explode('/',self::$web_modules_namespace . self::$callbacks[$route]);

            // Collect the last index of the array
            $last = end($parts);

            // Grab the controller name and method call
            $segments = explode('@',$last);

            //blacklist of method
            if (in_array($segments[1], ['filters'])) {
              return false;
            }

            // Instanitate controller
            $controller = new $segments[0]();

            $filterRes = self::runFilters($controller, $_REQUEST);
            if (!$filterRes) {
                return false;
            }

            // Call method
            $controller->{$segments[1]}();

            if (self::$halts) return;
          } else {
            // Call closure
            call_user_func(self::$callbacks[$route]);

            if (self::$halts) return;
          }
        }
      }
    } else {
      // Check if defined with regex
      $pos = 0;
      foreach (self::$routes as $route) {
        if (strpos($route, ':') !== false) {
          $route = str_replace($searches, $replaces, $route);
        }

        if (preg_match('#^' . $route . '$#', $uri, $matched)) {
          if (self::$methods[$pos] == $method || self::$methods[$pos] == 'ANY') {
            $found_route = true;

            // Remove $matched[0] as [1] is the first parameter.
            array_shift($matched);

            //带:any和:num的路由名才能传参，整理参数
            if (count($matched) > 0) {
                $tempParams = [];
                foreach ($matched as $item) {
                    $item = trim($item, '/');
                    $item = explode('/', $item);
                    if (empty($item[0])) {
                        array_shift($item);//弹出第一个空参数
                    }
                    $tempParams = array_merge($tempParams, $item);
                }
                $matched = $tempParams;
            }

            if (!is_object(self::$callbacks[$pos])) {

              // Grab all parts based on a / separator
              $parts = explode('/',self::$web_modules_namespace . self::$callbacks[$pos]);

              // Collect the last index of the array
              $last = end($parts);

              // Grab the controller name and method call
              $segments = explode('@',$last);

              //blacklist of method
              if (in_array($segments[1], ['filters'])) {
                  return false;
              }

              // Instanitate controller
              $controller = new $segments[0]();

              $filterRes = self::runFilters($controller, array_merge($matched, $_REQUEST));
              if (!$filterRes) {
                  return false;
              }

              // Fix multi parameters
              if(!method_exists($controller, $segments[1])) {
                echo "controller and action not found";
              } else {
                call_user_func_array(array($controller, $segments[1]), $matched);
              }

              if (self::$halts) return;
            } else {
              call_user_func_array(self::$callbacks[$pos], $matched);

              if (self::$halts) return;
            }
          }
        }
        $pos++;
      }
    }

    // Run the error callback if the route was not found
    if ($found_route == false) {
      if (!self::$error_callback) {
        self::$error_callback = function() {
          header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
          echo '404: ' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . ' Not Found！';
        };
      } else {
        if (is_string(self::$error_callback)) {
          self::get($_SERVER['REQUEST_URI'], self::$error_callback);
          self::$error_callback = null;
          self::dispatch();
          return ;
        }
      }
      call_user_func(self::$error_callback);
    }
  }

  public static function runFilters($callbacks, $matched = [])
  {
      //过滤器数组
      if (method_exists($callbacks, 'filters')) {
          $filters = call_user_func_array([$callbacks, 'filters'], []);
          if ($filters && is_array($filters)) {
              foreach ($filters as $filter) {
                  if (!class_exists($filter)) {
                      continue;
                  }
                  $reflectionClass = new \ReflectionClass($filter);
                  if (!$reflectionClass->IsInstantiable()) { //是否可实例化
                      continue;
                  }
                  if (!$reflectionClass->hasMethod('init')) { //方法是否存在
                      continue;
                  }

                  if (!$reflectionClass->hasMethod('preFilter')) { //方法是否存在
                      continue;
                  }

                  unset($reflectionClass);
                  $instance = new $filter;
                  $instance->init();
                  $res = $instance->preFilter($matched);
                  unset($instance);
                  if (empty($res)) {
                      return false;
                  }
              }
          }
      }
      return true;
  }
}
