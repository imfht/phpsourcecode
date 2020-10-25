<?php

require_once('../src/microAOP/Proxy.php');

class Model {

    public function foo() {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

class AspectOne {

    public function fooBefore($params) {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

    public function fooAfter($params) {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

class AspectTwo {

    public function fooBefore($params) {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

class AspectThree {

    public function fooAfter($params) {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

$model = new Model();

//Bind multi aspect objects
microAOP\Proxy::__bind__($model, 'AspectOne', 'AspectTwo', 'AspectThree');

$model->foo();

/*  output:

------------------------------------------
AspectOne::fooBefore has been executed
------------------------------------------
AspectTwo::fooBefore has been executed
Model::foo has been executed
------------------------------------------
AspectOne::fooAfter has been executed
------------------------------------------
AspectThree::fooAfter has been executed

*/