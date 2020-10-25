<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-14
 * Time: 下午3:47.
 */
use MiotApi\Contract\Instance\Property;

class PropertyTest extends PHPUnit_Framework_TestCase
{
    private $property;

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function setUp()
    {
        $data = '{
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
                }';

        $this->property = new Property(json_decode($data, true));
    }

    public function tearDown()
    {
        $this->property = null;
    }

    public function testInit()
    {
        $this->assertEquals('urn:miot-spec-v2:property:brightness:0000000D:yeelink-bslamp1:1', $this->property->getType());
    }

    public function testGetIid()
    {
        $this->assertEquals(2, $this->property->getIid());
    }

    public function testVerify()
    {
        $this->assertTrue($this->property->verify(50));
        $this->assertFalse($this->property->verify(101));
        $this->assertFalse($this->property->verify(50.01));
    }

    public function testCanRead()
    {
        $this->assertTrue($this->property->canRead());
    }

    public function testCanWrite()
    {
        $this->assertTrue($this->property->canWrite());
    }

    public function testCanNotify()
    {
        $this->assertTrue($this->property->canNotify());
    }

    public function testGetSpecification()
    {
        $this->assertInstanceOf(\MiotApi\Contract\Specification\PropertySpecification::class, $this->property->getSpecification());
    }

    public function testGetSpecificationContext()
    {
        $this->assertEquals(json_encode(\MiotApi\Util\Jsoner\Jsoner::load('property/urn:miot-spec-v2:property:brightness:0000000D')), $this->property->getSpecificationContext());
    }
}
