<?php declare(strict_types = 1);
namespace msqphp\main\cookie;

use msqphp\base;
use msqphp\core;

trait CookieOperateTrait
{
    // 是否存在
    public function exists() : bool
    {
        return isset(static::$cookies[$this->getKey()]);
    }

    // 得到当前操作cookie值
    public function get()
    {
        return $this->getVal('decode');
    }
    // 得到全部cookie值
    public function getAll() : array
    {
        return static::$cookies;
    }
    // 设置cookie值
    public function set() : void
    {
        // 获取cookie信息
        $pointer   = $this->params;

        $key      = $this->getKey();
        $value    = $this->getVal('encode');

        $expire   = time() + ( $pointer['expire'] ?? static::$config['expire'] );
        $path     = $pointer['path']     ?? static::$config['path'];
        $domain   = $pointer['domain']   ?? static::$config['domain'];
        $secure   = $pointer['secure']   ?? static::$config['secure'];
        $httponly = $pointer['httponly'] ?? static::$config['httponly'];

        $func     = ( $pointer['transcoding'] ?? static::$config['transcoding'] ) ? 'setcookie' : 'setrawcookie';

        $func($key, $value, $expire, $path, $domain, $secure, $httponly) || static::exception('未知错误, 无法定义cookie');

        static::$cookies[$key] = $value;
    }

    // 删除cookie
    public function delete() : void
    {
        $key = $this->getKey();
        setcookie($key, '', 0);
        unset(static::$cookies[$key]);
    }
    // 清空cookie
    public function deleteAll() : void
    {
        $this->clear();
    }

    public function clear() : void
    {
        // 遍历
        foreach (static::$cookies as $key => $value) {
            setcookie($key, '', 0);
        }
        static::$cookies = [];
    }

    // 得到当前操作cookie正确键值
    private function getKey() : string
    {
        isset($this->params['key']) || static::exception('未选定任意cookie');

        return ($this->params['prefix'] ?? static::$config['prefix']).$this->params['key'];
    }
    private function getVal(string $type = '')
    {
        $pointer = $this->params;

        // 值存在,取值
        if (isset($pointer['value'])) {
            $value = $pointer['value'];
        } else {
            // 取cookies中值
            $key = $this->getKey();
            // 允许通过get获得null值, 但不允许加密(设置cookie)时获得null值
            if (!isset(static::$cookies[$key]) && $type === 'decode') {
                return null;
            } else {
                static::exception('cookie值不存在');
            }
            $value = static::$cookies[$key];
        }
        // 是否需要解码
        if ('encode' === $type) {
            if ((isset($pointer['encode']) && $pointer['encode']) || static::$config['encode']) {
                $value = $this->encodeValue($value);
            }
        // 是否需要加码
        } elseif ('decode' === $type) {
            if ((isset($pointer['decode']) && $pointer['decode'])  || static::$config['encode']) {
                $value = $this->decodeValue($value);
            }
        }

        return $value;
    }

    // 加密当前cookie值
    private function encodeValue($value) : string
    {
        return core\crypt\Crypt::encode(serialize($value));
    }

    // 解密当前cookie值
    private function decodeValue(string $value)
    {
        return unserialize(core\crypt\Crypt::decode($value));
    }
}