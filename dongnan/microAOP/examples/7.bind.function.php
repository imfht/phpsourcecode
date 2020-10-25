<?php

require_once('../src/microAOP/Proxy.php');

class Model {

    public function foo() {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

    public function fooOne() {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

    public function two() {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

    public function three() {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

function foo() {
    echo '------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . ' has been executed' . PHP_EOL;
}

function forRegex() {
    echo '------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . ' has been executed' . PHP_EOL;
}

$closure = function() {
    echo '------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . ' has been executed' . PHP_EOL;
};

class Aspect {

    static public function foo() {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

    public function two() {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}

$model = new Model();

//Bind function
microAOP\Proxy::__bind_func__($model, 'foo', 'before', 'foo');

//Bind closure
microAOP\Proxy::__bind_func__($model, 'foo', 'after', $closure);

//Another way to bind closure
microAOP\Proxy::__bind_func__($model, 'three', 'after', function() {
    echo __FUNCTION__ . ' has been executed' . PHP_EOL;
});

//Bind Static method of class
microAOP\Proxy::__bind_func__($model, 'fooOne', 'before', 'Aspect::foo');

//Bind method of object
microAOP\Proxy::__bind_func__($model, 'two', 'before', array(new Aspect(), 'two'));

//Support for regex
microAOP\Proxy::__bind_func__($model, '/^foo.*/i', 'always', 'forRegex');

$model->foo();
echo '==========================================' . PHP_EOL;
$model->fooOne();
echo '==========================================' . PHP_EOL;
$model->two();
echo '==========================================' . PHP_EOL;
$model->three();


/*  output:

------------------------------------------
foo has been executed
Model::foo has been executed
------------------------------------------
{closure} has been executed
------------------------------------------
forRegex has been executed
==========================================
------------------------------------------
Aspect::foo has been executed
Model::fooOne has been executed
------------------------------------------
forRegex has been executed
==========================================
------------------------------------------
Aspect::two has been executed
Model::two has been executed
==========================================
Model::three has been executed
{closure} has been executed

*/