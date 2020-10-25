<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-14
 * Time: 上午10:52.
 */
use MiotApi\Contract\Instance\Instance;

class InstanceTest extends PHPUnit_Framework_TestCase
{
    private $instance;

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function setUp()
    {
        $urn = 'urn:miot-spec-v2:device:light:0000A001:yeelink-bslamp1:1';

        $this->instance = new Instance($urn);
    }

    public function tearDown()
    {
        $this->instance = null;
    }

    public function testInit()
    {
        $this->assertEquals('urn:miot-spec-v2:device:light:0000A001:yeelink-bslamp1:1', $this->instance->getType());
        foreach ($this->instance->getServicesNode() as $service) {
            $this->assertInstanceOf(\MiotApi\Contract\Instance\Service::class, $service);
        }
    }

    public function testGetServicesNode()
    {
        foreach ($this->instance->getServicesNode() as $index => $service) {
            $this->assertInstanceOf(\MiotApi\Contract\Instance\Service::class, $service);
            $this->assertEquals($index, $service->getIid());
        }
    }

    public function testGetPropertiesNode()
    {
        foreach ($this->instance->getPropertiesNode(2) as $index => $property) {
            $this->assertInstanceOf(\MiotApi\Contract\Instance\Property::class, $property);
            $this->assertEquals($index, $property->getIid());
        }
    }

    public function testGetPropertiesNodes()
    {
        foreach ($this->instance->getPropertiesNodes() as $index => $property) {
            $this->assertInstanceOf(\MiotApi\Contract\Instance\Property::class, $property);
        }
    }

    public function testService()
    {
        $this->assertInstanceOf(\MiotApi\Contract\Instance\Service::class, $this->instance->service(2));
        $this->assertEquals(2, $this->instance->service(2)->getIid());
    }

    public function testProperty()
    {
        $this->assertInstanceOf(\MiotApi\Contract\Instance\Property::class, $this->instance->property(2, 2));
        $this->assertEquals([
            0,
            100,
            1,
        ], $this->instance->property(2, 2)->getValueRange());
    }

    public function testGetSidPidByName()
    {
        $name = 'color';
        list($sid, $pid) = $this->instance->getSidPidByName($name);
        $this->assertEquals([2], $sid);
        $this->assertEquals([3], $pid);
    }

    public function testGetSpecification()
    {
        $this->assertInstanceOf(\MiotApi\Contract\Specification\DeviceSpecification::class, $this->instance->getSpecification());
    }

    public function testGetSpecificationContext()
    {
        $this->assertEquals(json_encode(\MiotApi\Util\Jsoner\Jsoner::load('device/urn:miot-spec-v2:device:light:0000A001')), $this->instance->getSpecificationContext());
    }
}
