<?php declare(strict_types = 1);

// 容器函数
function app()
{
    return \msqphp\core\container\Container::getInstance();
}