<?php

global $php;

$config = $php->config['rabbitmq'][$php->factory_key];
if (empty($config) or empty($config['host'])) {
    throw new Exception("require rabbitmq[$php->factory_key] config.");
}

if (empty($config['port'])) {
    $config['port'] = 5672;
}

if (empty($config['user'])) {
    $config['user'] = 'guest';
}

if (empty($config['pass'])) {
    $config['pass'] = 'guest';
}

if (empty($config['vhost'])) {
    $config['vhost'] = '/';
}

if (empty($config['debug'])) {
    $config['debug'] = true;
}

$rabbitmq = new App\Component\RabbitMQ($config);

return $rabbitmq;
