<?php declare(strict_types = 1);
namespace msqphp\test\core\traits;

class GetTest extends \msqphp\test\Test
{
    public function testStart() : void
    {
        $this->init();
        $this->testThis();
    }
    public function testCallFunc1() : void
    {
        $this->object(new \msqphp\test\core\traits\resource\TestClass)
             ->property('testGet1')
             ->value(true)
             ->test();
    }
}