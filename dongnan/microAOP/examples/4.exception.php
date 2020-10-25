<?php

require_once('../src/microAOP/Proxy.php');

class Model {

    public function err() {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
        throw new Exception("Oops! There's an exception!");
    }

}

class Aspect {

    public function errException($params) {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
        var_dump($params['exception']);
    }

}

$model = new Model();

//Just bind it
microAOP\Proxy::__bind__($model, new Aspect());

$model->err();
/*  output:

Model::err has been executed
------------------------------------------
Aspect::errException has been executed
object(Exception)#5 (7) {
  ["message":protected]=>
  string(27) "Oops! There's an exception!"
  ["string":"Exception":private]=>
  string(0) ""
  ["code":protected]=>
  int(0)
  ["file":protected]=>
  string(46) "/var/www/microaop/examples/4.exception.php"
  ["line":protected]=>
  int(11)
  ["trace":"Exception":private]=>
  array(5) {
    [0]=>
    array(4) {
      ["function"]=>
      string(3) "err"
      ["class"]=>
      string(5) "Model"
      ["type"]=>
      string(2) "->"
      ["args"]=>
      array(0) {
      }
    }
    [1]=>
    array(6) {
      ["file"]=>
      string(44) "/var/www/microaop/src/microAOP/Proxy.php"
      ["line"]=>
      int(309)
      ["function"]=>
      string(10) "invokeArgs"
      ["class"]=>
      string(16) "ReflectionMethod"
      ["type"]=>
      string(2) "->"
      ["args"]=>
      array(2) {
        [0]=>
        object(Model)#1 (0) {
        }
        [1]=>
        array(0) {
        }
      }
    }
    [2]=>
    array(6) {
      ["file"]=>
      string(44) "/var/www/microaop/src/microAOP/Proxy.php"
      ["line"]=>
      int(325)
      ["function"]=>
      string(8) "__call__"
      ["class"]=>
      string(14) "microAOP\Proxy"
      ["type"]=>
      string(2) "::"
      ["args"]=>
      array(6) {
        [0]=>
        object(Model)#1 (0) {
        }
        [1]=>
        &string(5) "Model"
        [2]=>
        &string(3) "err"
        [3]=>
        &array(0) {
        }
        [4]=>
        &array(1) {
          ["Aspect"]=>
          object(Aspect)#2 (0) {
          }
        }
        [5]=>
        &array(0) {
        }
      }
    }
    [3]=>
    array(6) {
      ["file"]=>
      string(46) "/var/www/microaop/examples/4.exception.php"
      ["line"]=>
      int(33)
      ["function"]=>
      string(6) "__call"
      ["class"]=>
      string(14) "microAOP\Proxy"
      ["type"]=>
      string(2) "->"
      ["args"]=>
      array(2) {
        [0]=>
        &string(3) "err"
        [1]=>
        &array(0) {
        }
      }
    }
    [4]=>
    array(6) {
      ["file"]=>
      string(46) "/var/www/microaop/examples/4.exception.php"
      ["line"]=>
      int(33)
      ["function"]=>
      string(3) "err"
      ["class"]=>
      string(14) "microAOP\Proxy"
      ["type"]=>
      string(2) "->"
      ["args"]=>
      array(0) {
      }
    }
  }
  ["previous":"Exception":private]=>
  NULL
}

*/