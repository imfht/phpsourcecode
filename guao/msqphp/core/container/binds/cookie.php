<?php declare(strict_types = 1);
namespace msqphp\core\container;

use msqphp\main\cookie\Cookie;

return [
    'shared' => false,
    'object' => function () {
        return new Cookie();
    },
];