<?php declare(strict_types = 1);
namespace msqphp\main\controller;

use msqphp\core;

return function(string $msg, ?string $url = null, bool $exit = true, bool $return = false) {
    return core\response\Response::alert($msg, $url, $exit, $return);
};