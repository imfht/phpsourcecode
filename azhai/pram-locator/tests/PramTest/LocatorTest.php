<?php
namespace PramTest;
use \PHPUnit_Framework_TestCase as TestCase;


class LocatorTest extends TestCase
{
    public $locator;
    
    public function setUp()
    {
        $this->locator = \Pram\Locator::getInstance();
    }

    public function test01AddNamespace()
    {
        $this->locator->addNamespace('NotORM', VENDOR_ROOT . '/NotORM');
        $now = new \NotORM_Literal('NOW()');
        $this->assertEquals(strval($now), 'NOW()');
    }

    public function test02AddClass()
    {
        $this->locator->addClass(VENDOR_ROOT . '/NotORM/NotORM.php',
                'NotORM', 'NotORM_Result', 'NotORM_Row', 
                'NotORM_Literal', 'NotORM_Structure');
        $now = new \NotORM_Literal('NOW()');
        $this->assertEquals(strval($now), 'NOW()');
    }
}

