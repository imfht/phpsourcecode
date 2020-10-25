<?php declare(strict_types = 1);
namespace msqphp\test\core\route;

final class RouteTest extends \msqphp\test\Test
{
    public function testStart() : void
    {
        $this->class('\msqphp\core\route\Route');
        $this->testThis();
    }
    public function testRoute() : void
    {
    }
}