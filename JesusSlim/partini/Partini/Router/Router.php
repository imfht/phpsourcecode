<?php

/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/27
 * Time: 下午5:35
 */

namespace Partini\Router;

use Partini\ApplicationInterface;
use Partini\HttpContext\Context;
use Partini\HttpContext\Output;
use Closure;

class Router
{

    protected $context;

    protected $routes;

    protected $middleWareStack;

    public static $METHODS = array('GET','HEAD','POST','PUT','PATCH','DELETE','OPTIONS');

    public function __construct(ApplicationInterface $context)
    {
        $this->context = $context;
    }

    public function getContext(){
        return $this->context;
    }

    public function handle(){
        //dispatch
        /** @var  \Partini\HttpContext\Context $ctx */
        $ctx = $this->context->produce(Context::class);
        $route = $this->dispatch($ctx->input()->method(),$this->cleanUri($ctx->input()->uriForRoute()));
        if($route !== false){
            //find
            //route handle
            /** @var \Partini\Router\Route $route */
            $response = $route->run($ctx);
            if(! $response instanceof Output){
                $response = is_null($response) ? $ctx->output() : $ctx->output()->body($response);
            }
            $response->send();
        }else{
            //not found
            throw new RouteException('route '.$ctx->input()->uriForRoute().' not found');
        }
    }

    protected function addRoute($methods,$uri,$action,$controller){
        $uri = empty($this->context->getConfig('BASE_PATH')) ? $this->cleanUri($uri) : $this->context->getConfig('BASE_PATH').$this->cleanUri($uri);
        $controller = empty($this->context->getConfig('CONTROLLER_NAME_SPACE')) ? $controller : (strpos($controller,$this->context->getConfig('CONTROLLER_NAME_SPACE')) !== false ? $controller : $this->context->getConfig('CONTROLLER_NAME_SPACE').$controller);
        $route = new Route($this,$methods,$uri,$action,$controller);
        if(!empty($this->middleWareStack)){
            $route->mid(end($this->middleWareStack));
        }
        foreach ($methods as $method){
            $this->routes[$method][$uri] = $route;
        }
        return $route;
    }

    public function get($uri,$action,$controller = null){
        return $this->addRoute(['GET','HEAD'],$uri,$action,$controller);
    }

    public function post($uri,$action,$controller = null){
        return $this->addRoute(['POST'],$uri,$action,$controller);
    }

    public function put($uri,$action,$controller = null){
        return $this->addRoute(['PUT'],$uri,$action,$controller);
    }

    public function patch($uri,$action,$controller = null){
        return $this->addRoute(['PATCH'],$uri,$action,$controller);
    }

    public function delete($uri,$action,$controller = null){
        return $this->addRoute(['DELETE'],$uri,$action,$controller);
    }

    public function options($uri,$action,$controller = null){
        return $this->addRoute(['OPTIONS'],$uri,$action,$controller);
    }

    public function all($uri,$action,$controller = null){
        return $this->addRoute(array_slice(self::$METHODS,0,-1),$uri,$action,$controller);
    }

    public function dispatch($method,$uri){
        return isset($this->routes[$method][$uri]) ? $this->routes[$method][$uri] : false;
    }

    public function cleanUri($uri){
        if($uri[0] !== '/') $uri = '/'.$uri;
        if(substr($uri,-1) === '/') $uri = substr($uri,0,-1);
        return $uri;
    }

    public function group($middleWares,Closure $c){
        $this->middleWareStack[] = empty($this->middleWareStack) ? $middleWares : array_merge(end($this->middleWareStack),$middleWares);
        call_user_func($c);
        array_pop($this->middleWareStack);
    }
}