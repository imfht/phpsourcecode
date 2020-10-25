<?php
namespace workerbase\classs;
require_once(__DIR__.'/../inc/marcfowler/macaw/Macaw.php');
/**
 * 路由类
 * Class Router
 * @method static Router get(string $route, Callable $callback)
 * @method static Router post(string $route, Callable $callback)
 * @method static Router put(string $route, Callable $callback)
 * @method static Router delete(string $route, Callable $callback)
 * @method static Router options(string $route, Callable $callback)
 * @method static Router head(string $route, Callable $callback)
 * @method static Router any(string $route, Callable $callback)
 * @method static Router getPathInfo()
 * @package workerbase\classs
 */
class Router extends \marcfowler\macaw\Macaw{

}
