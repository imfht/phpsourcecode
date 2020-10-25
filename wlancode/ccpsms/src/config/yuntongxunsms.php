<?php

return [
    //说明：主账号，登陆云通讯网站后，可在"控制台-应用"中看到开发者主账号ACCOUNT SID。
    'accountSid' => '',
    //说明：主账号Token，登陆云通讯网站后，可在控制台-应用中看到开发者主账号AUTH TOKEN。
    'accountToken' => '',
    //(正式：8a48b5514a146b84014a1475f3a50005，沙箱：)
    //说明：应用Id，如果是在沙盒环境开发，请配置"控制台-应用-测试DEMO"中的APPID。如切换到生产环境，请使用自己创建应用的APPID。
    'appId' => '8a216da856c131340156de914c361b16',
    //说明：生成环境请求地址：app.cloopen.com
    'serverUri' => 'https://app.cloopen.com',
    //说明：请求端口 ，无论生产环境还是沙盒环境都为8883
    'serverPort' => '8883',
    //说明：REST API版本号保持不变
    'softVersion' => '2013-12-26',
];