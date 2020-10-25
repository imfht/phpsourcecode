<?php declare(strict_types = 1);
namespace msqphp\main\controller;

use msqphp\core;

return function($json_data, bool $exit = true, bool $return = false) {
    return core\response\Response::json($json_data, $exit, $return);
};