<?php

namespace Cutest\Widget;

use \PHPUnit_Framework_TestCase as TestCase;
use \Cute\Contrib\Widget\Counter;
use \Cute\Cache\TextCache;
use \Cute\Cache\MemoryCache;
use \Cute\Memory\RedisExt;

class CounterTest extends TestCase
{

    protected static $counter = null;

    public static function setUpBeforeClass()
    {
        self::$counter = new Counter('test_val', -1);
    }

    public static function tearDownAfterClass()
    {
        self::$counter->delete();
    }

    public function test01RedisIncrease()
    {
        $redis = new RedisExt();
        $cache = new MemoryCache($redis, self::$counter->getName());
        self::$counter->attach($cache);
        $val = self::$counter->increase();
        $this->assertEquals(0, $val);
        $this->assertEquals(0, $cache->readData());
    }

    public function test02TextIncrease()
    {
        $cache = new TextCache(self::$counter->getName());
        self::$counter->attach($cache);
        $val = self::$counter->increase();
        $this->assertEquals(1, $val);
        $this->assertEquals(1, $cache->readData());
    }

}
