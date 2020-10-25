<?php declare(strict_types = 1);
namespace msqphp\test\core\traits;

class CallStaticTest extends \msqphp\test\Test
{
    public function testStart() : void
    {
        $this->init();
        $this->class('\msqphp\test\core\traits\resource\TestClass');
        $this->testThis();
    }
    public function testCallStaticFunc1() : void
    {
        $this->method('testCallStaticMethod1')->args()->result(NULL)->test();
    }
}