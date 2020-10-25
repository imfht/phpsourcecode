<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-14
 * Time: 下午3:34.
 */
use MiotApi\Contract\Instance\Service;

class ServiceTest extends PHPUnit_Framework_TestCase
{
    private $service;

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function setUp()
    {
        $data = '
        {
            "iid": 2,
            "type": "urn:miot-spec-v2:service:light:00007802:yeelink-bslamp1:1",
            "description": "Light",
            "properties": [
                {
                    "iid": 1,
                    "type": "urn:miot-spec-v2:property:on:00000006:yeelink-bslamp1:1",
                    "description": "Switch Status",
                    "format": "bool",
                    "access": [
                        "read",
                        "write",
                        "notify"
                    ]
                },
                {
                    "iid": 2,
                    "type": "urn:miot-spec-v2:property:brightness:0000000D:yeelink-bslamp1:1",
                    "description": "Brightness",
                    "format": "uint8",
                    "access": [
                        "read",
                        "write",
                        "notify"
                    ],
                    "value-range": [
                        0,
                        100,
                        1
                    ],
                    "unit": "percentage"
                },
                {
                    "iid": 3,
                    "type": "urn:miot-spec-v2:property:color:0000000E:yeelink-bslamp1:1",
                    "description": "Color",
                    "format": "uint32",
                    "access": [
                        "read",
                        "write",
                        "notify"
                    ],
                    "value-range": [
                        0,
                        16777215,
                        1
                    ],
                    "unit": "rgb"
                }
            ]
        }';

        $this->service = new Service(json_decode($data, true));
    }

    public function tearDown()
    {
        $this->service = null;
    }

    public function testInit()
    {
        $this->assertEquals('urn:miot-spec-v2:service:light:00007802:yeelink-bslamp1:1', $this->service->getType());
        foreach ($this->service->getPropertiesNode() as $property) {
            $this->assertInstanceOf(\MiotApi\Contract\Instance\Property::class, $property);
        }
    }

    public function testGetIid()
    {
        $this->assertEquals(2, $this->service->getIid());
    }

    public function testGetPropertiesNode()
    {
        foreach ($this->service->getPropertiesNode() as $property) {
            $this->assertInstanceOf(\MiotApi\Contract\Instance\Property::class, $property);
        }
    }

    public function testProperty()
    {
        $this->assertInstanceOf(\MiotApi\Contract\Instance\Property::class, $this->service->property(2));
    }

    public function testGetSpecification()
    {
        $this->assertInstanceOf(\MiotApi\Contract\Specification\ServiceSpecification::class, $this->service->getSpecification());
    }

    public function testGetSpecificationContext()
    {
        $this->assertEquals(json_encode(\MiotApi\Util\Jsoner\Jsoner::load('service/urn:miot-spec-v2:service:light:00007802')), $this->service->getSpecificationContext());
    }
}
