<?php declare (strict_types = 1);
namespace msqphp\main\cache;

trait CacheOperateTrait
{
    // 当前处理缓存键是否存在
    public function exists(): bool
    {
        return HAS_CACHE && $this->getHander()->available($this->getKey());
    }
    public function available(): bool
    {
        return $this->exists();
    }

    // 得到当前处理缓存键对应值
    public function get()
    {
        try {
            return HAS_CACHE ? $this->getHander()->get($this->getKey()) : null;
        } catch (handlers\CacheHandlerException $e) {
            static::exception($this->getKey() . '缓存无法获取,原因:' . $e->getMessage());
        }
    }

    // 自增
    public function inc(): int
    {
        return $this->increment();
    }
    public function increment(): int
    {
        if (HAS_CACHE) {

            try {
                return $this->getHander()->increment($this->getKey(), $this->params['offset'] ?? 1);
            } catch (handlers\CacheHandlerException $e) {
                static::exception($this->getKey() . '缓存无法自增,原因:' . $e->getMessage());
            }
        }
        return -1;
    }

    // 自减
    public function dec(): int
    {
        return $this->decrement();
    }
    public function decrement(): int
    {
        if (HAS_CACHE) {
            try {
                return $this->getHander()->decrement($this->getKey(), $this->params['offset'] ?? 1);
            } catch (handlers\CacheHandlerException $e) {
                static::exception($this->getKey() . '缓存无法自减,原因:' . $e->getMessage());
            }
        }
        return -1;
    }

    // 设置当前处理缓存键 对应值
    public function set(): void
    {
        if (HAS_CACHE) {
            try {
                $this->getHander()->set($this->getKey(), $this->getValue(), $this->getExpire());
            } catch (handlers\CacheHandlerException $e) {
                static::exception($this->getKey() . '缓存无法赋值,原因:' . $e->getMessage());
            }
        }
    }

    // 删除当前处理缓存键
    public function delete(): void
    {
        try {
            $this->getHander()->delete($this->getKey());
        } catch (handlers\CacheHandlerException $e) {
            static::exception($this->getKey() . '缓存无法删除,原因:' . $e->getMessage());
        }
    }

    // 清楚所有过期缓存
    public function clear(): void
    {
        try {
            $this->getHander()->clear();
        } catch (handlers\CacheHandlerException $e) {
            static::exception('缓存无法清空,原因:' . $e->getMessage());
        }
    }
    public function flush(): void
    {
        $this->clear();
    }
}
