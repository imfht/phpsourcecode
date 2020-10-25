<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-14
 * Time: 下午7:32.
 */
use MiotApi\Api\BaseApi;

class BaseApiTest extends PHPUnit_Framework_TestCase
{
    private $api;

    public function setUp()
    {
        $this->api = new BaseApi(getenv('appId'), getenv('accessToken'));
    }

    public function tearDown()
    {
        $this->api = null;
    }

    public function testDevices()
    {
        $this->assertArrayHasKey('devices', $this->api->devices(true));
    }

    public function testDeviceInformation()
    {
        $this->assertArrayHasKey('device-information', $this->api->deviceInformation([
            'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA55ZWVsaW5rLWNvbG9AyMRUUGAg0NTk4OTg3NRVoAA',
            'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA15ZWVsaW5rLW1vbm8xFRQYCDEzMTgwNzc2FWYA',
        ]));
    }

    public function testProperties()
    {
        $this->assertArrayHasKey('properties', $this->api->properties([
            'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA55ZWVsaW5rLWNvbG9AyMRUUGAg0NTk4OTg3NRVoAA.2.1',
            'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA55ZWVsaW5rLWNvbG9AyMRUUGAg0NTk4OTg3NRVoAA.2.2',
        ]));
    }

    public function testSetProperties()
    {
        $properties = [
            'properties' => [
                [
                    'pid'   => 'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA15ZWVsaW5rLW1vbm8xFRQYCDEzMTgwNzc2FWYA.2.2',
                    'value' => 75,
                ],
            ],
            /*"voice" => [
                "recognition" => "设置灯的亮度为70",
                "semantics" => "xxxxx",
            ]*/
        ];

        $requestInfo = $this->api->setProperties($properties);

        $this->assertEquals(0, $requestInfo['properties'][0]['status']);

        $getInfo = $this->api->properties('M1GAxtaW9A0LXNwZWMtdjIVgoAFGA15ZWVsaW5rLW1vbm8xFRQYCDEzMTgwNzc2FWYA.2.2');
        $this->assertEquals(75, $getInfo['properties'][0]['value']);
    }

    public function testScenes()
    {
        $this->assertArrayHasKey('scenes', $this->api->scenes());
    }

    public function testTriggerScene()
    {
        $scene_id = '1031976223';

        $this->assertArrayHasKey('oid', $this->api->triggerScene($scene_id));
    }

    public function testHomes()
    {
        $this->assertArrayHasKey('homes', $this->api->homes());
    }

    public function testSubscript()
    {
        $properties = [
            'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA15ZWVsaW5rLW1vbm8xFRQYCDEzMTgwNzc2FWYA.2.2',
            'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA55ZWVsaW5rLWNvbG9AyMRUUGAg0NTk4OTg3NRVoAA.2.1',
        ];
        $receiverUrl = 'https://cloud-cn.yeelight.com/';
        $customData = [
            'test' => 'test',
        ];
        $requestInfo = $this->api->subscript($properties, $customData, $receiverUrl);
        $this->assertArrayHasKey('properties', $requestInfo);
    }

    public function testUnSubscript()
    {
        $properties = [
            'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA15ZWVsaW5rLW1vbm8xFRQYCDEzMTgwNzc2FWYA.2.2',
            'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA55ZWVsaW5rLWNvbG9AyMRUUGAg0NTk4OTg3NRVoAA.2.1',
        ];

        $requestInfo = $this->api->unSubscript($properties);

        $this->assertArrayHasKey('properties', $requestInfo);
    }

    public function testGet()
    {
        $this->assertArrayHasKey('device-information', $this->api->get('/api/v1/device-information', [
            'dids' => 'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA15ZWVsaW5rLW1vbm8xFRQYCDEzMTgwNzc2FWYA',
        ]));
    }
}
