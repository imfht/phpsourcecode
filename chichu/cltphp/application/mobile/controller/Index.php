<?php
namespace app\mobile\controller;
use clt\Lunar;
class Index extends Common{
    public function initialize(){
        parent::initialize();
    }
    public function index(){
        return view();
    }
}