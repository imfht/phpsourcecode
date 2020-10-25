<?php
// +----------------------------------------------------------------------
// | RedisTest.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace Registry;

use App\Core\Registry\Persistent\Redis;

class RedisTest extends \UnitTestCase
{
    public function testBase()
    {
        $client1 = di('redis');
        $client2 = Redis::getInstance();
        $client3 = di('redis');

        $client1->set('test', 'client1');
        $client2->select(2);

        $this->assertEquals($client1->get('test'), $client3->get('test'));
        $this->assertFalse($client1->get('test') == $client2->get('test'));

        $client3->select(2);
        $client3->set('test', 'client3');
        $this->assertEquals($client1->get('test'), $client3->get('test'));

    }
}