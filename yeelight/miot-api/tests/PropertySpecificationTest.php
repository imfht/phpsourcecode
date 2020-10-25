<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-13
 * Time: 下午4:17.
 */
use MiotApi\Contract\Specification\PropertySpecification;

class PropertySpecificationTest extends PHPUnit_Framework_TestCase
{
    private $property;

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function testInit()
    {
        $urn = 'urn:miot-spec-v2:property:air-quality:0000001C';

        $this->property = new PropertySpecification($urn);

        $this->assertEquals('urn:miot-spec-v2:property:air-quality:0000001C', $this->property->getType());
    }

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function testGetValueRange()
    {
        $urn = 'urn:miot-spec-v2:property:keep-warm-temperature:0000002E';

        $this->property = new PropertySpecification($urn);

        $this->assertEquals([
            0,
            100,
            1,
        ], $this->property->getValueRange());
    }

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function testGetFormat()
    {
        $urn = 'urn:miot-spec-v2:property:air-quality:0000001C';

        $this->property = new PropertySpecification($urn);

        $this->assertEquals('uint8', $this->property->getFormat());
    }

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function testGetAccess()
    {
        $urn = 'urn:miot-spec-v2:property:air-quality:0000001C';

        $this->property = new PropertySpecification($urn);

        $this->assertEquals([
            'read',
            'notify',
        ], $this->property->getAccess());
    }

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function testGetValueList()
    {
        $urn = 'urn:miot-spec-v2:property:air-quality:0000001C';

        $this->property = new PropertySpecification($urn);

        $this->assertEquals([
            [
                'value'       => 1,
                'description' => 'Excellent',
            ],
            [
                'value'       => 2,
                'description' => 'Fine',
            ],
        ], $this->property->getValueList());
    }

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function testGetUnit()
    {
        $urn = 'urn:miot-spec-v2:property:keep-warm-temperature:0000002E';

        $this->property = new PropertySpecification($urn);

        $this->assertEquals('celsius', $this->property->getUnit());
    }
}
