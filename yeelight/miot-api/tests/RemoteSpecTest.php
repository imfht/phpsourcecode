<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-12
 * Time: 下午5:52.
 */
use MiotApi\Contract\RemoteSpec;

class RemoteSpecTest extends PHPUnit_Framework_TestCase
{
    public function testInstances()
    {
        $this->assertNotFalse(RemoteSpec::instances());
        $this->assertArrayHasKey('instances', RemoteSpec::instances());
    }

    public function testProperties()
    {
        $this->assertNotFalse(RemoteSpec::properties());
        $this->assertArrayHasKey('types', RemoteSpec::properties());
    }

    public function testActions()
    {
        $this->assertNotFalse(RemoteSpec::actions());
        $this->assertArrayHasKey('types', RemoteSpec::actions());
    }

    public function testEvents()
    {
        $this->assertNotFalse(RemoteSpec::events());
        $this->assertArrayHasKey('types', RemoteSpec::events());
    }

    public function testServices()
    {
        $this->assertNotFalse(RemoteSpec::services());
        $this->assertArrayHasKey('types', RemoteSpec::services());
    }

    public function testDevices()
    {
        $this->assertNotFalse(RemoteSpec::devices());
        $this->assertArrayHasKey('types', RemoteSpec::devices());
    }

    public function testInstance()
    {
        $this->assertNotFalse(RemoteSpec::instance('urn:miot-spec-v2:device:light:0000A001:yeelink-bslamp1:1'));
        $this->assertArrayHasKey('type', RemoteSpec::instance('urn:miot-spec-v2:device:light:0000A001:yeelink-bslamp1:1'));
    }

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function testProperty()
    {
        $this->assertNotFalse(RemoteSpec::property('urn:miot-spec-v2:property:color:0000000E:yeelink-bslamp1:1'));
        $this->assertArrayHasKey('type', RemoteSpec::property('urn:miot-spec-v2:property:color:0000000E:yeelink-bslamp1:1'));
    }

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function testAction()
    {
        $this->assertNotFalse(RemoteSpec::action('urn:miot-spec-v2:action:play:0000280B'));
        $this->assertArrayHasKey('type', RemoteSpec::action('urn:miot-spec-v2:action:play:0000280B'));
    }

    public function testEvent()
    {
    }

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function testService()
    {
        $this->assertNotFalse(RemoteSpec::service('urn:miot-spec-v2:service:light:00007802:yeelink-bslamp1:1'));
        $this->assertArrayHasKey('type', RemoteSpec::service('urn:miot-spec-v2:service:light:00007802:yeelink-bslamp1:1'));
    }

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function testDevice()
    {
        $this->assertNotFalse(RemoteSpec::device('urn:miot-spec-v2:device:light:0000A001'));
        $this->assertArrayHasKey('type', RemoteSpec::device('urn:miot-spec-v2:device:light:0000A001'));
    }

    public function testFetch()
    {
        $this->assertNotNull(RemoteSpec::fetch(RemoteSpec::INSTANCES));
        $this->assertContains('instances', RemoteSpec::fetch(RemoteSpec::INSTANCES));
    }
}
