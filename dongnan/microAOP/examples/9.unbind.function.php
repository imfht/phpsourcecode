<?php

require_once('../src/microAOP/Proxy.php');

class Model {

    public function foo() {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

function fooBefore() {
    echo __FUNCTION__ . ' has been executed' . PHP_EOL;
    echo '------------------------------------------' . PHP_EOL;
}

function fooAfter() {
    echo '------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . ' has been executed' . PHP_EOL;
}

$model = new Model();

//Bind function
microAOP\Proxy::__bind_func__($model, 'foo', 'before', 'fooBefore');
microAOP\Proxy::__bind_func__($model, 'foo', 'after', 'fooAfter');

$model->foo();

//取消绑定Model::foo中before位置的函数
microAOP\Proxy::__unbind_func__($model, 'foo', 'before');

echo '==========================================' . PHP_EOL;

$model->foo();


/*  output:

fooBefore has been executed
------------------------------------------
Model::foo has been executed
------------------------------------------
fooAfter has been executed
==========================================
Model::foo has been executed
------------------------------------------
fooAfter has been executed

*/