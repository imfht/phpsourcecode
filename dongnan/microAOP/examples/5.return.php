<?php

require_once('../src/microAOP/Proxy.php');

class Model {

    public function ret() {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
        return ['status' => true, 'data' => "This's a return"];
    }

}

class Aspect {

    public function retAfter($params) {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
        print_r($params['return']);
    }

}

$model = new Model();

//Just bind it
microAOP\Proxy::__bind__($model, new Aspect());

$model->ret();

/*  output:

Model::ret has been executed
------------------------------------------
Aspect::retAfter has been executed
Array
(
    [status] => 1
    [data] => This's a return
)

*/