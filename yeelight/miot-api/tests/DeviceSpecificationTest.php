<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-13
 * Time: 上午11:05.
 */
use MiotApi\Contract\Specification\DeviceSpecification;

class DeviceSpecificationTest extends PHPUnit_Framework_TestCase
{
    private $device;

    public function setUp()
    {
        $urn = 'urn:miot-spec-v2:device:light:0000A001';

        $this->device = new DeviceSpecification($urn);
    }

    public function testInit()
    {
        $this->assertEquals('urn:miot-spec-v2:device:light:0000A001', $this->device->getType());
    }

    public function testGetRequiredServices()
    {
        foreach ($this->device->getRequiredServices() as $requiredService) {
            $this->assertInstanceOf(\MiotApi\Contract\Specification\ServiceSpecification::class, $requiredService);
        }
    }

    public function testGetOptionalServices()
    {
        foreach ($this->device->getOptionalServices() as $optionalService) {
            $this->assertInstanceOf(\MiotApi\Contract\Specification\ServiceSpecification::class, $optionalService);
        }
    }
}
