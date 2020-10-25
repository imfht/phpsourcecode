<?php declare(strict_types = 1);
namespace msqphp\test\core\traits;

class CallTest extends \msqphp\test\Test
{
    public function testStart() : void
    {
        $this->init();
        $this->object(new \msqphp\test\core\traits\resource\TestClass);
        $this->testThis();
    }
    public function testCallFunc1() : void
    {
        $this->method('testCallMethod1')->args()->result(NULL)->test();
    }
}