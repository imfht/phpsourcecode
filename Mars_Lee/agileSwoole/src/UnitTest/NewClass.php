<?php


namespace UnitTest;


class NewClass
{
        static $instance;

        protected $id;
        public function process(){
                $b=new B;
                $process = new Process(function () use($b){
                        $b->test();
                });
                $this->id =  $process->id;
                echo $process->id.PHP_EOL;
        }

        /**
         * @return mixed
         */
        public function getId()
        {
                return $this->id;
        }


        public static function getInstance()
        {
                if(self::$instance == null) {
                        self::$instance = new self;
                }
                return self::$instance;
        }
}


class Process {
        public $id = 0;
        public function __construct(\Closure $closure)
        {
               $this->id = rand(1,100);
               $closure();
        }
}


class B {
        protected $id;
        public function test() {
                echo 'A'.PHP_EOL;
                echo NewClass::getInstance()->getId().PHP_EOL;
        }
}

$c = NewClass::getInstance();
$c->process();
$c->process();