<?php

require_once('../src/microAOP/Proxy.php');

class Model {

    public function args($one, $two, $three = 'three') {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

class Aspect {

    public function argsAlways($params) {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
        print_r($params['args']);
    }

}

$model = new Model();

//Just bind it
microAOP\Proxy::__bind__($model, new Aspect());

$model->args('arg1', 'argTwo');

echo "===========================================" . PHP_EOL;

$model->args('one', 'two', 'argThree');

/*  output:

Model::args has been executed
------------------------------------------
Aspect::argsAlways has been executed
Array
(
    [one] => arg1
    [two] => argTwo
    [three] => three
)
===========================================
Model::args has been executed
------------------------------------------
Aspect::argsAlways has been executed
Array
(
    [one] => one
    [two] => two
    [three] => argThree
)

*/