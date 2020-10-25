<?php

require_once __DIR__.'/../../vendor/autoload.php';

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

$app->withFacades(true,[
    '\Yuntongxun\Facades\YuntongxunSms'=>'YuntongxunSms'
]);

$app->withEloquent();

$app->register(\Yuntongxun\Providers\YuntongxunSmsServiceProvider::class);

return $app;