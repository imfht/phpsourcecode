<?php declare(strict_types = 1);
namespace msqphp\main\cookie;

final class Cookie
{
    // 指针trait
    use CookieParamsTrait, CookieOperateTrait, CookieStaticTrait;

    // 抛出异常
    private static function exception(string $message) : void
    {
        throw new CookieException($message);
    }
}