<?php

/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/14
 * Time: 下午6:45
 */
class EZController
{
    protected $view;

    function __construct(){
    }

    public function getView(){
        return EZView();
    }

    public function render(){
        EZView()->render();
    }
}