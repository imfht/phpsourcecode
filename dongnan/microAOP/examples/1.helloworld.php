<?php

require_once('../src/microAOP/Proxy.php');

class Model {

    public function save() {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

class Aspect {

    public function saveBefore($params) {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

    public function saveAfter($params) {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

$model = new Model();

//Just bind it
microAOP\Proxy::__bind__($model, new Aspect());

$model->save();

/*  output:

------------------------------------------
Aspect::saveBefore has been executed
Model::save has been executed
------------------------------------------
Aspect::saveAfter has been executed

*/