<?php
namespace Cutest;

use \PHPUnit_Framework_TestCase as TestCase;


class CommonTest extends TestCase
{
    public function test01StartsWith()
    {
        $this->assertTrue(function_exists('starts_with'));
        $this->assertTrue(starts_with('starts_with', 'start'));
        $this->assertFalse(starts_with('start', 'starts_with'));
        $this->assertTrue(starts_with('starts_with', ''));
    }

    public function test02EndsWith()
    {
        $this->assertTrue(function_exists('ends_with'));
        $this->assertTrue(ends_with('ends_with', 'ith'));
        $this->assertFalse(ends_with('ith', 'ends_with'));
        $this->assertTrue(ends_with('ends_with', ''));
    }

    public function test03ToUTF8()
    {
        $this->assertTrue(function_exists('convert'));
        $word = '中文Englis混合' . __FUNCTION__;
        $this->assertEquals($word, convert($word));
        $this->assertEquals($word, convert(iconv('UTF-8', 'GBK', $word)));
    }

    public function test04ExecFunctionArray()
    {
        $this->assertTrue(function_exists('exec_function_array'));
        $result = exec_function_array('strtolower', [__FUNCTION__]);
        $this->assertEquals(strtolower(__FUNCTION__), $result);
        $args = range(11, 20);
        $result = exec_function_array(new Sample(), $args);
        $this->assertEquals(array_sum($args), $result);
    }

    public function test05ExecMethodArray()
    {
        $this->assertTrue(function_exists('exec_method_array'));
        $result = exec_method_array(new Sample(), 'sum', range(11, 14));
        $this->assertEquals(array_sum(range(11, 14)) + 99, $result);
        $result = exec_method_array(new Sample(), 'sum', range(11, 15));
        $this->assertEquals(array_sum(range(11, 15)), $result);
        $class = __NAMESPACE__ . '\\Sample';
        $result = exec_method_array($class, 'sum', range(11, 13));
        $this->assertEquals(array_sum(range(11, 13)) + 88 + 99, $result);
        $result = exec_method_array($class, 'sum', range(11, 15));
        $this->assertEquals(array_sum(range(11, 15)), $result);
    }

    public function test06ExecConstructArray()
    {
        $this->assertTrue(function_exists('exec_construct_array'));
        $class = __NAMESPACE__ . '\\Sample';
        $result = exec_construct_array($class, range(11, 14));
        $this->assertEquals(range(11, 14), $result->data);
        $result = exec_construct_array($class, range(11, 15));
        $this->assertEquals(range(11, 15), $result->data);
    }
}


class Sample
{
    public $data = null;

    public function __construct()
    {
        $this->data = func_get_args();
    }

    public static function sum($a, $b, $c, $d = 88, $e = 99)
    {
        $args = func_num_args() > 5 ? func_get_args() : [$a, $b, $c, $d, $e];
        return array_sum($args);
    }

    public function __invoke($a, $b, $c, $d = 88, $e = 99)
    {
        $args = func_num_args() > 5 ? func_get_args() : [$a, $b, $c, $d, $e];
        return array_sum($args);
    }
}
