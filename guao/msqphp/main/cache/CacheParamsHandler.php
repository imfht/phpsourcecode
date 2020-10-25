<?php declare (strict_types = 1);
namespace msqphp\main\cache;

trait CacheParamsHandler
{

    // 得到缓存真实键
    private function getKey(): string
    {
        // 不存在异常
        isset($this->params['key']) || static::exception('未选择任意缓存键');
        // 添加前缀
        return ($this->params['prefix'] ?? static::$config['prefix']) . $this->params['key'];
    }
    // 得到缓存值
    private function getValue()
    {
        isset($this->params['value']) || static::exception('未给当前缓存设置任意赋值');

        return $this->params['value'];
    }
    // 得到过期时间
    private function getExpire(): int
    {
        return $this->params['expire'] ?? static::$config['expire'];
    }

    // 得到缓存处理器
    private function getHander(): handlers\CacheHandlerInterface
    {
        if (!isset($this->params['handler'])) {
            return $this->params['handler'] = static::getCacheHandler($this->params['type'] ?? null, $this->params['config'] ?? []);
        }
        return $this->params['handler'];
    }
}
