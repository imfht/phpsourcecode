<?php declare (strict_types = 1);
namespace msqphp\main\cache;

trait CacheParamsTrait
{
    // 当前处理类的指针
    private $params = [];

    /**
     * @param  string  $type   处理类种类
     * @param  array   $config 处理类配置
     * @param  handlers\CacheHandlerInterface $handler  处理类
     * @param  string  $prefix 前缀
     * @param  string  $key    键
     * @param  miexd   $value  值
     * @param  int     $offset 偏移量
     * @param  int     $expire 过期时间
     *
     */

    public function __construct()
    {
        $this->init();
    }
    // 初始化指针
    public function init(): self
    {
        static::initStatic();
        // 将当前操作cache初始化
        $this->params = [];
        return $this;
    }
    // 添加一个params值
    private function setParamValue(string $key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }
    // 缓存处理器类型
    public function type(string $type): self
    {
        return $this->setParamValue('type', $type);
    }
    // 缓存处理器配置
    public function config(array $config): self
    {
        return $this->setParamValue('config', $config);
    }
    // 设置处理类
    public function handler(handlers\CacheHandlerInterface $handler): self
    {
        return $this->setParamValue('handler', $handler);
    }
    // 设置当前缓存处理键前缀
    public function prefix(string $prefix): self
    {
        return $this->setParamValue('prefix', $prefix);
    }
    // 设置当前处理缓存键
    public function key(string $key): self
    {
        return $this->setParamValue('key', $key);
    }
    // 当前处理缓存值
    public function value($value): self
    {
        return $this->setParamValue('value', $value);
    }
    // 当前处理缓存偏移量
    public function offset(int $offset): self
    {
        return $this->setParamValue('offset', $offset);
    }
    // 设置当前处理缓存过期时间
    public function expire(int $expire): self
    {
        return $this->setParamValue('expire', $expire);
    }
}
