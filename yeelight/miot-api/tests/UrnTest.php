<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-11
 * Time: 下午6:10.
 */
use MiotApi\Contract\Urn;
use MiotApi\Exception\SpecificationErrorException;
use PHPUnit\Framework\TestCase;

class UrnTest extends TestCase
{
    private $urnObj;

    public function setUp()
    {
        // 参考 http://miot-spec.org/miot-spec-v2/instances
        $urn = 'urn:miot-spec-v2:device:light:0000A001:yeelink-bslamp1:1';

        $this->urnObj = new Urn($urn);
    }

    public function tearDown()
    {
        $this->urnObj = null;
    }

    public function testGetExpression()
    {
        $this->assertEquals($this->urnObj->getExpression(), 'urn:miot-spec-v2:device:light:0000A001:yeelink-bslamp1:1');
    }

    /**
     * @depends testGetExpression
     */
    public function test__construct()
    {
    }

    public function testSetUrn()
    {
        $this->urnObj->setUrn('urn');
        $this->assertEquals($this->urnObj->getUrn(), 'urn');
    }

    /**
     * @depends testSetUrn
     */
    public function testGetUrn()
    {
        $this->assertEquals($this->urnObj->getUrn(), 'urn');

        return $this->urnObj->getUrn();
    }

    /**
     * @expectedException  MiotApi\Exception\SpecificationErrorException
     */
    public function testSetUrnException()
    {
        $this->urnObj->setUrn('test');
        $this->expectException(SpecificationErrorException::class);
    }

    public function testSetNamespace()
    {
        $this->urnObj->setNamespace('miot-spec-v2');
        $this->assertEquals($this->urnObj->getNamespace(), 'miot-spec-v2');
    }

    /**
     * @depends testSetNamespace
     */
    public function testGetNamespace()
    {
        $this->assertEquals($this->urnObj->getNamespace(), 'miot-spec-v2');

        return $this->urnObj->getNamespace();
    }

    public function testSetType()
    {
        $this->urnObj->setType('device');
        $this->assertEquals($this->urnObj->getType(), 'device');
    }

    /**
     * @depends testSetType
     */
    public function testGetType()
    {
        $this->assertEquals($this->urnObj->getType(), 'device');

        return $this->urnObj->getType();
    }

    public function testSetName()
    {
        $this->urnObj->setName('light');
        $this->assertEquals($this->urnObj->getName(), 'light');
    }

    /**
     * @depends testSetName
     */
    public function testGetName()
    {
        $this->assertEquals($this->urnObj->getName(), 'light');

        return $this->urnObj->getName();
    }

    public function testSetValue()
    {
        $this->urnObj->setValue('0000A001');
        $this->assertEquals($this->urnObj->getValue(), '0000A001');
    }

    /**
     * @depends testSetValue
     */
    public function testGetValue()
    {
        $this->assertEquals($this->urnObj->getValue(), '0000A001');

        return $this->urnObj->getValue();
    }

    public function testSetVendorProduct()
    {
        $this->urnObj->setVendorProduct('yeelink-bslamp1');
        $this->assertEquals($this->urnObj->getVendorProduct(), 'yeelink-bslamp1');
    }

    /**
     * @depends testSetVendorProduct
     */
    public function testGetVendorProduct()
    {
        $this->assertEquals($this->urnObj->getVendorProduct(), 'yeelink-bslamp1');

        return $this->urnObj->getVendorProduct();
    }

    public function testSetVersion()
    {
        $this->assertEquals($this->urnObj->getVersion(), '1');

        return $this->urnObj->getVersion();
    }

    /**
     * @depends testSetVersion
     */
    public function testGetVersion()
    {
        $this->assertEquals($this->urnObj->getVersion(), '1');

        return $this->urnObj->getVersion();
    }
}
