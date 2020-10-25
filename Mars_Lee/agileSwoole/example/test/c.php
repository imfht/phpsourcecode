<?php

class A{
        protected $arr = [];
        protected $ss = [];
        function ff()
        {
                foreach ($this->ss as $a) {
                        call_user_func($a);
                }
                echo 'a';
                foreach ($this->arr as $a) {
                        call_user_func($a);
                }
        }

        function add(Closure $closure) {
                $this->arr[] = $closure;
        }
};

function b()
{
        echo 'b';
}
$a = new A();

$a->add(function (){
        b();
});

$a->ff();
