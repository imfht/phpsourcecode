<?php declare(strict_types = 1);
namespace msqphp\core\container;

use msqphp\main\session\Session;

return [
    'shared' => false,
    'object' => function () {
        return new Session();
    },
];