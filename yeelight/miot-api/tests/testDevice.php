<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-8
 * Time: 下午3:39.
 */
require_once '../vendor/autoload.php';

use MiotApi\Contract\Specification\DeviceSpecification;
use MiotApi\Contract\Urn;

$context = '
{
    "type": "urn:miot-spec-v2:device:light:0000A001:yeelink-bslamp1:1",
    "description": "Light",
    "services": [
        {
            "iid": 1,
            "type": "urn:miot-spec-v2:service:device-information:00007801:yeelink-bslamp1:1",
            "description": "Device Information",
            "properties": [
                {
                    "iid": 1,
                    "type": "urn:miot-spec-v2:property:manufacturer:00000001:yeelink-bslamp1:1",
                    "description": "Device Manufacturer",
                    "format": "string",
                    "access": [
                        "read"
                    ]
                },
                {
                    "iid": 2,
                    "type": "urn:miot-spec-v2:property:model:00000002:yeelink-bslamp1:1",
                    "description": "Device Model",
                    "format": "string",
                    "access": [
                        "read"
                    ]
                },
                {
                    "iid": 3,
                    "type": "urn:miot-spec-v2:property:serial-number:00000003:yeelink-bslamp1:1",
                    "description": "Device Serial Number",
                    "format": "string",
                    "access": [
                        "read"
                    ]
                },
                {
                    "iid": 4,
                    "type": "urn:miot-spec-v2:property:name:00000004:yeelink-bslamp1:1",
                    "description": "Device Name",
                    "format": "string",
                    "access": [
                        "read"
                    ]
                },
                {
                    "iid": 5,
                    "type": "urn:miot-spec-v2:property:firmware-revision:00000005:yeelink-bslamp1:1",
                    "description": "Current Firmware Version",
                    "format": "string",
                    "access": [
                        "read"
                    ]
                }
            ]
        },
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
        }
    ]
}
';

$urn = new Urn('urn:miot-spec-v2:device:light:0000A001:yeelink-bslamp1:1');
$device = new DeviceSpecification($context);

// 根据键获取值
$services = $device->services;

//var_dump($services);

var_dump($device);
