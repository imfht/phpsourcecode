<?php declare(strict_types = 1);
namespace msqphp\main\cookie;

trait CookieParamsTrait
{
    /**
     * @param  string $prefix 前缀
     * @param  string $key 键
     * @param  string|array $value 值(如果是数组则需要加密)
     * @param  int    $expire 前缀
     * @param  string $path 路径
     * @param  string $domain 域名
     * @param  bool $secure 安全传输
     * @param  bool $httponly httponly
     * @param  bool $transcoding bool
     * @param  bool   $decode 解密
     * @param  bool   $encode 加密
     */

    private $params = [];

    public function __construct()
    {
        static::initStatic();
        $this->init();
    }

    // 初始化
    public function init() : self
    {
        $this->params = [];
        return $this;
    }
    // 添加一个params值
    private function setParamsValue(string $key, $value) : self
    {
        $this->params[$key] = $value;
        return $this;
    }
    // 设置前缀
    public function prefix(string $prefix) : self
    {
        return $this->setParamsValue('prefix', $prefix);
    }

    // 设置键
    public function key(string $key) : self
    {
        return $this->setParamsValue('key', $key);
    }
    // 设置值
    public function value($value) : self
    {
        return $this->setParamsValue('value', $value);
    }
    // 设置前缀
    public function expire(int $expire) : self
    {
        return $this->setParamsValue('expire', $expire);
    }
    // 设置路径
    public function path(string $path) : self
    {
        return $this->setParamsValue('path', $path);
    }
    // 设置域名
    public function domain(string $domain) : self
    {
        return $this->setParamsValue('domain', $domain);
    }
    // 设置 是否仅https
    public function secure(bool $secure = true) : self
    {
        return $this->setParamsValue('secure', $secure);
    }
    // 设置 httponly
    public function httponly(bool $httponly = true) : self
    {
        return $this->setParamsValue('httponly', $httponly);
    }
    // 是否url转码
    public function transcoding(bool $transcoding = false) : self
    {
        return $this->setParamsValue('transcoding', $transcoding);
    }
    // 值解密
    public function decode(bool $decode = true) : self
    {
        return $this->setParamsValue('decode', $decode);
    }
    // 值加密
    public function encode(bool $encode = true) : self
    {
        return $this->setParamsValue('encode', $encode);
    }
}