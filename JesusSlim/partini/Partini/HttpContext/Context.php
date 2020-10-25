<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/8/3
 * Time: 下午5:12
 */

namespace Partini\HttpContext;


use Inject\Injector;
use Partini\ApplicationInterface;

class Context extends Injector
{

    protected $KeysInside = array(
        Context::class,
    );

    protected $input;
    protected $output;
    protected $parent;

    public function __construct(ApplicationInterface $parent)
    {
        $this->mapData(Context::class,$this);
        $this->input = new Input();
        $this->output = new Output($this);
        $this->parent = $parent;
    }

    public function getParent(){
        return $this->parent;
    }

    public function input(){
        return $this->input;
    }

    public function output(){
        return $this->output;
    }

    public function stash($k,$v){
        if(!$this->keyValid($k)) return false;
        $this->mapData($k,$v);
        return true;
    }

    public function getStash($k){
        return $this->produce($k);
    }

    public function mapLazy($k,$class){
        if(!$this->keyValid($k)) return false;
        $this->mapSingleton($k,$class);
        return true;
    }

    public function keyValid($key){
        return !in_array($key,$this->KeysInside);
    }

    public function getCookie($key){
        return $this->input()->cookie($key);
    }

    public function setCookie($k,$v,...$params){
        $this->output()->cookie($k,$v,...$params);
    }

    public function redirect($url){
        $this->output()->header("Location", $url);
        $this->output()->setStatus(Output::HTTP_FOUND);
    }
}