<?php
namespace Cutest\Web;

use \PHPUnit_Framework_TestCase as TestCase;
use \Cute\Web\Router;


class RouterTest extends TestCase
{
    protected $root = null;

    public function setUp()
    {
        $this->root = Router::getCurrent(); //根路由
        $this->root->expose(TEST_ROOT . '/public', '*.php');
        $this->root->expose(TEST_ROOT . '/public', '*/*.php');
    }

    public function test01OneLevel() //一段URL的路由
    {
        $route = $this->root->dispatch('/a/');
        $this->assertEquals('/a/', $route['url']);
        $this->assertCount(0, $route['args']);
    }

    public function test02TwoLevel() //两段URL的路由
    {
        $route = $this->root->dispatch('/b/cd/');
        $this->assertEquals('/b/cd/', $route['url']);
        $this->assertNotEmpty($route['args']);
        $this->assertCount(1, $route['args']);
        $this->assertEquals('cd', $route['args'][0]);
    }

    public function test03NotFound() //找不到路由，404
    {
        $route = $this->root->dispatch('/a/cd/');
        $this->assertCount(0, $route['handlers']);
    }

    public function test04ChildrenOrder() //路由次序，使用贪婪匹配
    {
        $route = $this->root->dispatch('/a/b/');
        $this->assertEquals('/a/b/', $route['url']);
        $this->assertCount(0, $route['args']);
    }
}

