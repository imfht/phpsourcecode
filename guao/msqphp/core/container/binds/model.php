<?php declare(strict_types = 1);
namespace msqphp\core\container;

use msqphp\main\model\Model;

return [
    'shared' => false,
    'object' => function () {
        return new Model();
    },
];