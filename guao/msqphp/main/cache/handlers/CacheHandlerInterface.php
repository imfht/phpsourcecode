<?php declare (strict_types = 1);
namespace msqphp\main\cache\handlers;

interface CacheHandlerInterface
{
    // 构造函数
    public function __construct(array $config);

    /**
     * @param  array  $config 缓存驱动配置
     * @param  string $key    缓存键
     * @param  string $val    缓存值
     * @param  int    $expire 缓存有效期
     * @param  int    $offset 偏移量
     * @throws CacheHandlerException
     */

    // 是否可用
    public function available(string $key): bool;
    // 得到缓存信息
    public function get(string $key);
    // 递增递减,当键不存在保存而非创建
    public function increment(string $key, int $offset): int;
    public function decrement(string $key, int $offset): int;
    // 设置缓存
    public function set(string $key, $value, int $expire): void;
    // 删除缓存
    public function delete(string $key): void;
    // 清空所有缓存
    public function clear(): void;
    // 抛出异常
    // private function exception(string $message) : void
    // {
    //  throw new CacheHandlerException($message);
    // }
}
