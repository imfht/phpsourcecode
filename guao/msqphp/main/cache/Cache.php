<?php declare (strict_types = 1);
namespace msqphp\main\cache;

final class Cache
{
    use CacheParamsTrait, CacheParamsHandler, CacheOperateTrait, CacheStaticTrait;

    // 抛出异常
    private static function exception(string $message): void
    {
        throw new CacheException($message);
    }
}
