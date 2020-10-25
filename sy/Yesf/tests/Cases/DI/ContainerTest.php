<?php
namespace YesfTest\DI;

use DateTime;
use PHPUnit\Framework\TestCase;
use Yesf\Yesf;
use Yesf\DI\Container;
use Yesf\Exception\NotFoundException;
use Yesf\Exception\InvalidClassException;
use Yesf\Exception\CyclicDependencyException;
use YesfApp\DI as DITest;

class ContainerTest extends TestCase {
	public static function setUpBeforeClass() {
		// Set alias
		Container::getInstance()->set('ClassOneAlias', DITest\ClassOne::class);
	}
	public function testNotFoundError() {
		$this->expectException(NotFoundException::class);
		Container::getInstance()->get('A\\Not\\Exists\\Clazz');
	}
	public function testInvalidError() {
		$this->expectException(InvalidClassException::class);
		Container::getInstance()->get(DITest\InvalidClass::class);
	}
	public function testCyclicDependencyError() {
		$this->expectException(CyclicDependencyException::class);
		Container::getInstance()->get(DITest\CyclicOne::class);
	}
	public function testGet() {
		$clazz = Container::getInstance()->get(DITest\TestClass::class);
		$this->assertNull(DITest\TestClass::$static_one);
		$this->assertEquals('one', $clazz->getOneResult());
		$this->assertEquals('two', $clazz->getTwo()->getName());
		$this->assertEquals('one', $clazz->one_alias->getName());
		$this->assertEquals('one', $clazz->obj1_from_constructor->getName());
		$this->assertEquals('two', $clazz->obj2_from_constructor->getName());
		$this->assertNull($clazz->plain_var);
		$this->assertEquals('', $clazz->plain_var_with_type);
		$this->assertEquals(10, $clazz->plain_var_with_default);
	}
	public function testHas() {
		$this->assertTrue(Container::getInstance()->has('ClassOneAlias'));
		$this->assertTrue(Container::getInstance()->has(DITest\TestClass::class));
		$this->assertTrue(Container::getInstance()->has(DITest\UnloadedClass::class));
		$this->assertFalse(Container::getInstance()->has('A\\Not\\Exists\\Clazz'));
	}
	public function testMulti() {
		Container::getInstance()->setMulti(DITest\MultiWithClone::class, Container::MULTI_CLONE);
		$obj1 = Container::getInstance()->get(DITest\MultiWithClone::class);
		$obj2 = Container::getInstance()->get(DITest\MultiWithClone::class);
		$obj3 = Container::getInstance()->get(DITest\MultiWithClone::class);
		// The first one is not cloned from others
		$this->assertFalse($obj1->cloned);
		$this->assertTrue($obj2->cloned);
		$this->assertTrue($obj3->cloned);
		$this->assertNotSame($obj1->id, $obj2->id);
		$this->assertNotSame($obj1->id, $obj3->id);
		Container::getInstance()->setMulti(DITest\MultiWithNew::class, Container::MULTI_NEW);
		$obj1 = Container::getInstance()->get(DITest\MultiWithNew::class);
		$obj2 = Container::getInstance()->get(DITest\MultiWithNew::class);
		$this->assertNotSame($obj1->id, $obj2->id);
	}
	public function testClosure() {
		$time = time();
		Container::getInstance()->set('MyTime', function() use ($time) {
			return new DateTime('@' . $time);
		});
		$it = Container::getInstance()->get(DITest\TestClosure::class);
		$this->assertSame($it->time->getTimestamp(), $time);
	}
}