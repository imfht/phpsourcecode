<p align="center"><img src="https://www.yeelight.com/yeelight201703/i/image/newindex/logo.png"></p>

<p align="center">
<a href="https://github.styleci.io/repos/136297421"><img src="https://github.styleci.io/repos/136297421/shield?branch=master" alt="StyleCI"></a>
<a href="https://scrutinizer-ci.com/g/Yeelight/miot-api/"><img src="https://scrutinizer-ci.com/g/Yeelight/miot-api/badges/quality-score.png?b=master" alt="Build Status"></a>
<a href="https://scrutinizer-ci.com/g/Yeelight/miot-api/"><img src="https://scrutinizer-ci.com/g/Yeelight/miot-api/badges/build.png?b=master" alt="Build Status"></a>
<a href="https://scrutinizer-ci.com/g/Yeelight/miot-api/"><img src="https://scrutinizer-ci.com/g/Yeelight/miot-api/badges/code-intelligence.svg?b=master" alt="Build Status"></a>
<a href="https://packagist.org/packages/yeelight/miot-api"><img src="https://poser.pugx.org/yeelight/miot-api/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/yeelight/miot-api"><img src="https://poser.pugx.org/yeelight/miot-api/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/yeelight/miot-api"><img src="https://poser.pugx.org/yeelight/miot-api/license.svg" alt="License"></a>
<a href="https://996.icu"><img src="https://img.shields.io/badge/link-996.icu-red.svg" alt="996.icu"></a>
</p>

# 小米IOT控制端API及小米IOT设备规范 SDK

## 安装

``` sh
composer require yeelight/miot-api
```

## 使用

``` php
$appId = 'Your App-Id';
// 用户oauth取得的accessToken
$accessToken = 'user access token';
$api = new Api($appId, $accessToken);

// 默认为大陆host，如果要获取其他地区设备的时候，可以setHost设置host
$api->setHost('api.home.mi.com');

// 一次性获取到包含了 serialNumber （原did）的设备列表
$devices = $api->devicesList();

// 根据 piid 获取 属性值 
$properties = $api->properties([
    'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA55ZWVsaW5rLWNvbG9AyMRUUGAg0NTk4OTg3NRVoAA.2.1',
    'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA55ZWVsaW5rLWNvbG9AyMRUUGAg0NTk4OTg3NRVoAA.2.2',
]);
        
$properties = [
    'properties' => [
        [
            "pid" => "M1GAxtaW9A0LXNwZWMtdjIVgoAFGA15ZWVsaW5rLW1vbm8xFRQYCDEzMTgwNzc2FWYA.2.2",
            "value" => 75
        ]
    ]
];

// 设置属性
$api->setProperties($properties);


// 读取用户在米家设置好的场景列表
$secenes = $api->scenes();

// 主动触发场景
$scene_id = '1031976223';

$api->triggerScene($scene_id);

// 读取家庭列表
$homes = $api->homes();

// 订阅属性变化
$properties = [
    'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA15ZWVsaW5rLW1vbm8xFRQYCDEzMTgwNzc2FWYA.2.2',
    'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA55ZWVsaW5rLWNvbG9AyMRUUGAg0NTk4OTg3NRVoAA.2.1',
];
$customData = [
    'test' => 'test'
];
$receiverUrl = 'https://www.xxx.com/receiver';
$api->subscript($properties, $customData, $receiverUrl);

// 退订属性变化
$api->unSubscript($properties);

// 优雅的设置单个设备属性
$did = 'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA55ZWVsaW5rLWNvbG9AyMRUUGAg0NTk2NTYwNRVoAA';
$type = "urn:miot-spec-v2:device:light:0000A001:yeelink-color1:1";
$data = [
    'on' => true,
    'brightness' => 99,
    'color-temperature' => 2100,
    'color' => 5777215
];
$requestInfo = $api->setPropertyGraceful($did, $type, $data);

// 优雅的设置多个设备属性
$data = [
    'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA55ZWVsaW5rLWNvbG9AyMRUUGAg0NTk2NTYwNRVoAA' => [
        'type' => 'urn:miot-spec-v2:device:light:0000A001:yeelink-color1:1',
        'data' => [
            'on' => true,
            'brightness' => 99,
            'color-temperature' => 2100,
            'color' => 2777215
        ]
    ],
    'M1GAxtaW9A0LXNwZWMtdjIVgoAFGAt5ZWVsaW5rLWN0MhUUGAg4NzEzMDQyMhWcCAA' => [
        'type' => 'urn:miot-spec-v2:device:light:0000A001:yeelink-ct2:1',
        'data' => [
            'on' => true,
            'brightness' => 50,
            'color-temperature' => 3500
        ]
    ]
];
$requestInfo = $api->setPropertiesGraceful($data);

// 优雅的获取单个设备属性
$did = 'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA55ZWVsaW5rLWNvbG9AyMRUUGAg0NTk2NTYwNRVoAA';
$type = "urn:miot-spec-v2:device:light:0000A001:yeelink-color1:1";
// data为空数组时，获取所有可读属性
$data = [
    'on',
    'brightness',
    'color-temperature',
    'color'
];
$attibutes = $api->getPropertyGraceful($did, $type, $data);

// 优雅的获取多个设备属性
$data = [
    'M1GAxtaW9A0LXNwZWMtdjIVgoAFGA55ZWVsaW5rLWNvbG9AyMRUUGAg0NTk2NTYwNRVoAA' => [
        'type' => 'urn:miot-spec-v2:device:light:0000A001:yeelink-color1:1',
        'data' => [
            'on',
            'brightness',
            'color-temperature',
            'color'
        ]
    ],
    'M1GAxtaW9A0LXNwZWMtdjIVgoAFGAt5ZWVsaW5rLWN0MhUUGAg4NzEzMDQyMhWcCAA' => [
        'type' => 'urn:miot-spec-v2:device:light:0000A001:yeelink-ct2:1',
        'data' => [] // 为空时，获取所有可读属性
    ]
];
$attibutes = $api->getPropertiesGraceful($data);

// 订阅设备的所有可订阅属性
$devices = $api->devicesList();
$customData = [
    'test' => 'test'
];
$receiverUrl = 'https://www.xxx.com/receiver';
$requestInfo = $api->subscriptByDevices($devices, $customData, $receiverUrl);

// 退订设备的所有订阅属性
$devices = $this->api->devicesList();
$requestInfo = $this->api->unSubscriptByDevices($devices);
```

## 参考资源

+ [miot-spec-doc](https://github.com/MiEcosystem/miot-spec-doc)

## Security Vulnerabilities

If you discover a security vulnerability within this library, please send an e-mail to Sheldon Lee via [lixiaodong@yeelight.com](mailto:lixiaodong@yeelight.com). All security vulnerabilities will be promptly addressed.


## License

This library is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
