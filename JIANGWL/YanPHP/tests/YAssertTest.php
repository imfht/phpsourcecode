<?php
/**
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/8/24
 * Time: 11:22
 */

namespace TestNamespace;

use PHPUnit\Framework\TestCase;
use Yan\Core\YAssert;

class YAssertTest extends TestCase
{
    public function testTrue()
    {
        $this->assertTrue(YAssert::true(1 === 1));
    }

    public function testAllTrue()
    {
        $this->assertTrue(YAssert::allTrue([true, 'a' => true]));
        $this->assertTrue(YAssert::true(true));
    }

    public function testEq(){
        $this->assertTrue(YAssert::eq(1,'1'));
    }

    public function testNull(){
        $this->assertTrue(YAssert::null(null));
    }

    /**
     * @expectedException \Yan\Core\Exception\YAssertionFailedException
     */
    public function testException()
    {
        $this->assertTrue(YAssert::true(1 == 2));
    }

    public function testBetween(){
        $this->assertTrue(YAssert::between(1,-1,100));
    }

    public function testAllBetween(){
        $this->assertTrue(YAssert::allBetween([1,2,3,4,-1,100],-1,100));
    }

    public function testBetweenLength(){
        $this->assertTrue(YAssert::betweenLength('abcd',1,4));
    }

    public function testIp(){
        $this->assertTrue(YAssert::ip('111.111.111.111'));
    }
}