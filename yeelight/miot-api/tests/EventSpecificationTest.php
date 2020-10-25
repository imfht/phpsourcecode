<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-13
 * Time: 下午3:36.
 */
class EventSpecificationTest extends PHPUnit_Framework_TestCase
{
    private $evnet;

    public function setUp()
    {
        //$urn = 'urn:miot-spec-v2:event:alert:00000007';

        //$this->evnet = new EventSpecification($urn);
    }

    public function tearDown()
    {
        $this->evnet = null;
    }

    public function testInit()
    {
        //$this->assertEquals('urn:miot-spec-v2:event:alert:00000007', $this->action->getType());
    }

    public function testGetArguments()
    {
    }
}
