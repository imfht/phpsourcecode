<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-13
 * Time: 下午3:16.
 */

namespace MiotApi\Contract\Specification;

class ActionSpecificationTest extends \PHPUnit_Framework_TestCase
{
    private $action;

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function setUp()
    {
        $urn = 'urn:miot-spec-v2:action:post:00002810';

        $this->action = new ActionSpecification($urn);
    }

    public function testInit()
    {
        $this->assertEquals('urn:miot-spec-v2:action:post:00002810', $this->action->getType());
    }

    public function testGetIn()
    {
        foreach ($this->action->getIn() as $item) {
            $this->assertInstanceOf(\MiotApi\Contract\Specification\PropertySpecification::class, $item);
        }
    }

    public function testGetOut()
    {
        foreach ($this->action->getOut() as $item) {
            $this->assertInstanceOf(\MiotApi\Contract\Specification\PropertySpecification::class, $item);
        }
    }
}
