<?php
/**
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/8/24
 * Time: 11:22
 */

namespace TestNamespace;

use Yan\Core\Compo\ResultInterface;
use Yan\Core\Exception\RuntimeException;
use Yan\Core\Exception\YanExceptionInterface;

class FunctionTest extends BaseTestCase
{
    function testIsCli()
    {
        $this->assertTrue(isCli());
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionCode -1
     * @expectedExceptionMessage error message
     */
    function testThrowErr()
    {
        throwErr('error message', -1, RuntimeException::class);
    }

    function testGenResult()
    {
        $this->assertInstanceOf(ResultInterface::class, genResult(1, 'test message', []));
    }

    function testGetClassName(){
        $this->assertEquals('ResultInterface',getClassName(ResultInterface::class));
    }


}