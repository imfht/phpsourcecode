<?php

require_once('../src/microAOP/Proxy.php');

class Model {

    public function save() {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

class AspectOne {

    public function saveBefore($params) {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
        echo '------------------------------------------' . PHP_EOL;
    }

}

class AspectTwo {

    public function saveBefore($params) {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
        echo '------------------------------------------' . PHP_EOL;
    }

}

$model = new Model();

//绑定切面类
microAOP\Proxy::__bind__($model, 'AspectOne', 'AspectTwo');

$model->save();

//取消绑定切面类:AspectOne
microAOP\Proxy::__unbind__($model, 'AspectOne');

echo '==========================================' . PHP_EOL;

$model->save();


/*  output:

AspectOne::saveBefore has been executed
------------------------------------------
AspectTwo::saveBefore has been executed
------------------------------------------
Model::save has been executed
==========================================
AspectTwo::saveBefore has been executed
------------------------------------------
Model::save has been executed

*/