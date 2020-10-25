<?php

require_once('../src/microAOP/HookInterface.php');
require_once('../src/microAOP/Hook.php');

use microAOP\Hook;
use microAOP\HookInterface;

class Model {

    public function save() {
        Hook::listen('save_begin');
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

class MyHook implements HookInterface {

    public function run(&$params) {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

Hook::load(['save_begin' => 'Myhook']);

$model = new Model();

$model->save();

/*  output:

MyHook::run has been executed
------------------------------------------
Model::save has been executed

*/
