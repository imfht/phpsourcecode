<?php declare(strict_types = 1);
namespace msqphp\core\container;

use msqphp\main\cache\Cache;

return [
    'shared' => false,
    'object' => function () {
        return new Cache();
    },
];