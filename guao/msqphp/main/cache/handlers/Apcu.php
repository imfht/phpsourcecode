<?php declare(strict_types = 1);
namespace msqphp\main\cache\handlers;

final class Apcu implements CacheHandlerInterface
{
    private $config = [
        'length' => 0
    ];

    public function __construct(array $config)
    {
        if (!function_exists('apcu_cache_info') || !ini_get('apcu.enabled')) {
            $this->exception('require Apcu support');
        }

        $this->config = array_merge($this->config, $config);
    }
    // 抛出异常
    private function exception(string $message) : void
    {
        throw new CacheHandlerException($message);
    }

    /**
     * @param  array  $config 缓存驱动配置
     * @param  string $key    缓存键
     * @param  string $val    缓存值
     * @param  int    $expire 缓存有效期
     * @return bool 是否成功 | 是否存在
     */
    // 是否可用
    public function available(string $key) : bool
    {
        return apcu_exists($key);
    }
    // 得到缓存信息
    public function get(string $key)
    {
        if (false !== $value = apcu_fetch($key)) {
            return $value;
        } else {
            $this->exception($key.'缓存键不存在');
        }
    }
    // 设置缓存
    public function set(string $key, $value, int $expire) : void
    {
        false === apcu_store($key, $value, $expire) && $this->exception($key.'缓存值无法设置');
        $this->config['length'] > 0 && $this->queue($key);
    }
    public function increment(string $key, int $offset) : int
    {
        if (false === $num = apcu_inc($key, $offset)) {
            $this->exception($key.'缓存值无法自增');
        } else {
            return $num;
        }
    }
    public function decrement(string $key, int $offset) : int
    {
        if (false === $num = apcu_dec($key, $offset)) {
            $this->exception($key.'缓存值无法自减');
        } else {
            return $num;
        }
    }
    // 清楚缓存
    public function delete(string $key) : void
    {
        false === apcu_delete($key) && $this->exception($key.'缓存值无法删除');
    }
    // 清除指定前缀的所有过期的缓存
    public function clear() : void
    {
        false === apcu_clear_cache('user') && $this->exception('缓存无法清空');
    }
    private function queue($key)
    {
        $queue_name = '__msq_cache_list__';
        if (!apcu_exists($queue_name)) {
            apcu_add($queue_name, [$key]);
        } else {
            $queue = apcu_fetch($queue_name);
            // 如果未找到则添加
            false === array_search($key, $queue) && array_push($queue, $key);
            // 如果队列长度大于配置长度
            if (count($queue) > $this->length) {
                // 移除第一个
                $old_key = array_shift($queue);
                // 删除对应文件
                apcu_delete($old_key);
            }
            // 重新写入
            apcu_store($queue_name, $queue);
        }
    }
}