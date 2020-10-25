<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/25
 * Time: 下午10:23
 */

namespace Inject;

use Closure;
class Chains
{

    /** @var  \Inject\InjectorInterface $context */
    protected $context;

    protected $handlers;

    protected $action;

    protected $req;

    public function __construct($context)
    {
        $this->context = $context;
        $this->handlers = [];
        $this->action = 'handle';
    }

    public function data($data){
        $this->req = $data;
        return $this;
    }

    public function chain($handlers = array()){
        $this->handlers = array_merge($this->handlers,is_array($handlers) ? $handlers : func_get_args());
        return $this;
    }

    public function action($action){
        $this->action = $action;
        return $this;
    }

    /***** run/runWith *****/
    // when use run/runWith,handler should be like "func($data,$next)" and return "$next($data)"

    public function run(){
        $handlers_registered = $this->handlers;
        $last_handler = array_pop($handlers_registered);
        $last = function($data) use ($last_handler){
            if($last_handler instanceof Closure){
                return call_user_func($last_handler,$data);
            }elseif (!is_object($last_handler)){
                $last_handler = $this->context->produce($last_handler);
            }
            $args = [$data];
            return call_user_func_array([$last_handler,$this->action],$args);
        };
        $handlers = array_reverse($handlers_registered);
        return call_user_func(array_reduce($handlers,$this->walk(),$last),$this->req);
    }

    public function runWith(Closure $call_back){
        $last = function($data) use ($call_back){
            return call_user_func($call_back,$data);
        };
        $handlers = array_reverse($this->handlers);
        return call_user_func(array_reduce($handlers,$this->walk(),$last),$this->req);
    }

    protected function walk(){
        return function($next_cb,$func_now){
            return function($data) use ($next_cb,$func_now){
                if($func_now instanceof Closure){
                    return call_user_func($func_now,$data,$next_cb);
                }elseif (!is_object($func_now)){
                    $func_now = $this->context->produce($func_now);
                }
                $args = [$data,$next_cb];
                return call_user_func_array([$func_now,$this->action],$args);
            };
        };
    }

    /***** runWild *****/
    // when use runWild , any handler return anything will break the loop and return

    public function runWild(){
        foreach ($this->handlers as $handler){
            $rtn = $handler instanceof Closure ? $this->context->call($handler,$this->req) : $this->context->callInClass($handler,$this->action,$this->req);
            if (!is_null($rtn)) return $rtn;
        }
        return null;
    }
}