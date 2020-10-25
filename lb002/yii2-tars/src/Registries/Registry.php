<?php

namespace Lxj\Yii2\Tars\Registries;

use Lxj\Yii2\Tars\Util;

class Registry
{
    public static function register($hostname, $port)
    {
        $tarsDriverConfig = Util::app()->params['tars'];

        foreach ($tarsDriverConfig['registries'] as $registry) {
            if ($registry['type'] === 'kong') {
                Kong::register($registry['url'], $hostname, $port);
            }
        }
    }

    public static function down($hostname, $port, $tarsDriverConfig)
    {
        foreach ($tarsDriverConfig['registries'] as $registry) {
            if ($registry['type'] === 'kong') {
                Kong::down($registry['url'], $hostname, $port);
            }
        }
    }
}
