<?php declare (strict_types = 1);
namespace msqphp\main\cache\handlers;

final class Memcached implements CacheHandlerInterface
{
    // 处理类
    private $memcached = null;
    // available时获取的值
    private $value  = [];
    private $config = [
        'length'  => 0,
        // 长连接,如果值不为空的话,推荐开启
        'name'    => 'msq_cache',
        // ip
        'server'  => '127.0.0.1',
        // 端口
        'port'    => 11211,
        // 权重
        'weight'  => 100,
        // 参数
        'options' => [],
        // 是否支持多
        'multi'   => false,
        // 多服务器配置
        'servers' => [],
    ];
    // 构造函数
    public function __construct(array $config)
    {
        extension_loaded('memcached') || $this->exception('require memcached support');

        $this->config = $config = array_merge($this->config, $config);
        // 获取实例
        $this->memcached = $memcached = new \Memcached($config['name']);

        // 是否是原始的
        if ($memcached->isPristine()) {
            if (!empty($config['options'])) {
                // 参数设置
                $memcached->setOptions($config['options']);
            }
            $memcached->addServer($config['server'], $config['port'], $config['weight']);
            if ($config['multi']) {
                $memcached->addServers($config['servers']);
            }
        }
    }

    // 抛出异常
    private function exception(string $message): void
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
    public function available(string $key): bool
    {
        if (false === $value = $this->memcached->get($key)) {
            return false;
        } else {
            $this->value[$key] = $value;
            return true;
        }
    }
    // 得到缓存信息
    public function get(string $key)
    {
        if (isset($this->value[$key])) {
            $result = $this->value[$key];
            unset($this->value[$key]);
            return $result;
        } else {
            if (false === $result = $this->memcached->get($key)) {
                return $result;
            } else {
                $this->exception($key . '缓存值无法获取');
            }
        }
    }
    public function increment(string $key, int $offset): int
    {
        if (false !== $result = $this->memcached->increment($key, $offset)) {
            return $result;
        } else {
            $this->exception($key . '缓存值无法自增');
        }
    }
    public function decrement(string $key, int $offset): int
    {
        if (false !== $result = $this->memcached->decrement($key, $offset)) {
            return $result;
        } else {
            $this->exception($key . '缓存值无法自减');
        }
    }
    // 设置缓存
    public function set(string $key, $value, int $expire): void
    {
        false === $this->memcached->set($key, $value, $expire) && $this->exception($key . '缓存值无法设置');
        // 如果限制了最大储存数, 调用队列
        $this->config['length'] > 0 && $this->queue($key);
    }
    // 删除缓存
    public function delete(string $key): void
    {
        false === $this->memcached->delete($key) && $this->exception($key . '缓存值无法删除');
    }
    // 清空缓存
    public function clear(): void
    {
        false === $this->memcached->flush() && $this->exception('缓存值无法清空');
    }
    private function queue($key)
    {
        $handler    = $this->memcached;
        $queue_name = '__msq_cache_list__';
        if (false === $queue = $handler->available($queue_name)) {
            $handler->set($queue_name, [$key]);
        } else {

            // 如果未找到则添加
            false === array_search($key, $queue) && array_push($queue, $key);
            // 如果队列长度大于配置长度
            if (count($queue) > $this->config['length']) {
                // 移除第一个
                $old_key = array_shift($queue);
                // 删除对应文件
                $handler->delete($old_key);
            }
            // 重新写入
            $handler->set($queue_name, $queue, PHP_INT_MAX);
        }
    }
}
