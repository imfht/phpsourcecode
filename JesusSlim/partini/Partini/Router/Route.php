<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/27
 * Time: 下午5:49
 */

namespace Partini\Router;


use Closure;
use Inject\Chains;

class Route
{

    const CLOSURE_ROUTE = 1;
    const COMMON_ROUTE = 2;

    protected $uri;
    protected $controller;
    protected $action;
    protected $methods;
    protected $type;
    protected $router;
    protected $midllewares;

    protected $context;

    public function __construct(Router $router,Array $methods,$uri,$action,$controller)
    {
        $this->router = $router;
        $this->uri = $uri;
        $this->methods = $methods;
        $this->midllewares = array();
        if($action instanceof Closure){
            $this->action = $action;
            $this->type = self::CLOSURE_ROUTE;
        }else{
            if(is_null($controller)){
                throw new RouteException("controller of $action is missing");
            }else{
                $this->action = $action;
                $this->controller = $controller;
                $this->type = self::COMMON_ROUTE;
            }
        }
    }

    public function mid($middlewares){
        $this->midllewares = array_merge($this->midllewares,$middlewares);
    }

    public function getMethods(){
        return $this->methods;
    }

    public function isClosure(){
        return $this->type == self::CLOSURE_ROUTE;
    }

    public function isCommon(){
        return $this->type == self::COMMON_ROUTE;
    }

    public function getAction(){
        return $this->action;
    }

    public function getController(){
        return $this->controller;
    }

    public function run($ctx){
        $chains = new Chains($this->router->getContext());
        return $chains->data($ctx)->chain($this->midllewares)->runWith(
            function(){
                if($this->isClosure()){
                    return $this->router->getContext()->call($this->action);
                }else{
                    return $this->router->getContext()->callInClass($this->controller,$this->action);
                }
            }
        );
    }
}