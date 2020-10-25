<?php declare(strict_types = 1);
namespace msqphp\main\controller;

use msqphp\core;

return function(bool $exit = true) {
    core\response\Response::refresh($exit);
};