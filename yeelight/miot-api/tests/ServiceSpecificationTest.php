<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-13
 * Time: 下午1:53.
 */
use MiotApi\Contract\Specification\ServiceSpecification;

class ServiceSpecificationTest extends PHPUnit_Framework_TestCase
{
    private $service;

    public function testInit()
    {
        $urn = 'urn:miot-spec-v2:service:light:00007802';

        $this->service = new ServiceSpecification($urn);

        $this->assertEquals('urn:miot-spec-v2:service:light:00007802', $this->service->getType());
    }

    public function testGetRequiredActions()
    {
        $urn = 'urn:miot-spec-v2:service:vacuum:00007810';

        $this->service = new ServiceSpecification($urn);

        foreach ($this->service->getRequiredActions() as $requiredAction) {
            $this->assertInstanceOf(\MiotApi\Contract\Specification\ActionSpecification::class, $requiredAction);
        }
    }

    public function testGetOptionalActions()
    {
        $urn = 'urn:miot-spec-v2:service:battery:00007805';

        $this->service = new ServiceSpecification($urn);

        foreach ($this->service->getOptionalActions() as $optionalAction) {
            $this->assertInstanceOf(\MiotApi\Contract\Specification\ActionSpecification::class, $optionalAction);
        }
    }

    public function testGetRequiredEvents()
    {
        /*$urn = 'urn:miot-spec-v2:event:alert1:00000007';

        $this->service = new ServiceSpecification($urn);

        foreach ($this->service->getRequiredEvents() as $requiredEvent) {
            $this->assertInstanceOf(\MiotApi\Contract\Specification\EventSpecification::class, $requiredEvent);
        }*/
    }

    public function testGetOptionalEvents()
    {
        /*$urn = 'urn:miot-spec-v2:event:alert1:00000007';

        $this->service = new ServiceSpecification($urn);

        foreach ($this->service->getOptionalEvents() as $optionalEvent) {
            $this->assertInstanceOf(\MiotApi\Contract\Specification\EventSpecification::class, $optionalEvent);
        }*/
    }

    public function testGetRequiredProperties()
    {
        $urn = 'urn:miot-spec-v2:service:battery:00007805';

        $this->service = new ServiceSpecification($urn);

        foreach ($this->service->getRequiredProperties() as $requiredProperty) {
            $this->assertInstanceOf(\MiotApi\Contract\Specification\PropertySpecification::class, $requiredProperty);
        }
    }

    public function testGetOptionalProperties()
    {
        $urn = 'urn:miot-spec-v2:service:battery:00007805';

        $this->service = new ServiceSpecification($urn);

        foreach ($this->service->getOptionalProperties() as $optionalProperty) {
            $this->assertInstanceOf(\MiotApi\Contract\Specification\PropertySpecification::class, $optionalProperty);
        }
    }
}
