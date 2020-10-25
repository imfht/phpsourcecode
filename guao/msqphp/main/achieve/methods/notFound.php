<?php declare(strict_types = 1);
namespace msqphp\main\controller;

use msqphp\core;

return function() {
    return core\response\Response::notFound();
};