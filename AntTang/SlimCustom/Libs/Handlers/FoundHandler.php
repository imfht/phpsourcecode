<?php
/**
 * @package     FoundHandler.php
 * @author      Jing Tang <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net/
 * @version     2.0
 * @copyright   Copyright (c) http://www.slimphp.net
 * @date        2017年7月31日
 */

namespace SlimCustom\Libs\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;

/**
 * Default route callback strategy with route parameters as an array of arguments.
 */
class FoundHandler implements InvocationStrategyInterface
{

    /**
     * Invoke a route callable with request, response, and all route parameters
     * as an array of arguments.
     *
     * @param array|callable $callable            
     * @param ServerRequestInterface $request            
     * @param ResponseInterface $response            
     * @param array $routeArguments            
     *
     * @return mixed
     */
    public function __invoke(callable $callable, ServerRequestInterface $request, ResponseInterface $response, array $routeArguments)
    {
        foreach ($routeArguments as $k => $v) {
            $request = $request->withAttribute($k, $v);
        }
        return call_user_func_array($callable, array_merge($routeArguments, [$request, $response]));
    }
}