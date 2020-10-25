<?php
namespace z;

class cache
{
    const LOCK_EXPIRE = 30; // 获取缓存锁的超时时间(秒)
    const LOCK_SLEEP = 1000; // 获取缓存锁的重试间隔(微秒)
    const LOCK_KEY_PREFIX = 'z-php-lock:'; // 缓存锁的键名前缀
    private static $Z_REDIS, $Z_MEMCACHED;
    public static function Redis(array $c = null, bool $new = false)
    {
        $c || $c = $GLOBALS['ZPHP_CONFIG']['REDIS'] ?? null;
        if (!$c) {
            throw new \Exception("没有配置redis连接参数");
        }

        if ($new) {
            $new = new \Redis();
            $new->connect($c['host'], $c['port'], $c['timeout'] ?? 1);
            return $new;
        }
        $key = "{$c['host']}:{$c['port']}";
        if (!isset(self::$Z_REDIS[$key])) {
            self::$Z_REDIS[$key] = new \Redis();
            self::$Z_REDIS[$key]->connect($c['host'], $c['port'], $c['timeout'] ?? 1);
            empty($c['pass']) || self::$Z_REDIS[$key]->auth($c['pass']);
            empty($c['database']) || self::$Z_REDIS[$key]->select($c['database']);
        }
        return self::$Z_REDIS[$key];
    }
    public static function Memcached(array $c = null)
    {
        $c || $c = $GLOBALS['ZPHP_CONFIG']['MEMCACHED'] ?? null;
        if (!$c) {
            throw new \Exception("没有配置memcached连接参数");
        }

        $key = md5(serialize($c));
        if (!isset(self::$Z_MEMCACHED[$key])) {
            self::$Z_MEMCACHED[$key] = new \Memcached();
            self::$Z_MEMCACHED[$key]->addServers($c);
        }
        return self::$Z_MEMCACHED[$key];
    }

    /**
     * redis锁
     * @param redis redis 连接实例
     * @param key 键名
     * @param expire 获取锁的超时时间（秒）
     * @return 成功返回锁的键名，否则返回false
     */
    public static function Rlock($redis, string $key, int $expire = 0)
    {
        $lock_key = self::LOCK_KEY_PREFIX . $key;
        if ($expire) {
            if (!$r = $redis->set($lock_key, 1, ['nx', 'ex' => $expire])) {
                $try = (int) $expire * 1000000 / self::LOCK_SLEEP - 1;
                do {
                    usleep(self::LOCK_SLEEP);
                    $r = $redis->set($lock_key, 1, ['nx', 'ex' => $expire]);
                } while (!$r && --$try);
            }
        } else {
            $r = $redis->set($lock_key, 1, ['nx', 'ex' => self::LOCK_EXPIRE]);
        }
        return $r ? $lock_key : false;
    }

    /**
     * memcached锁
     * @param mem memcached 连接实例
     * @param key 键名
     * @param expire 获取锁的超时时间（秒）
     * @return 成功返回锁的键名，否则返回false
     */
    public static function Mlock($mem, string $key, int $expire = 0)
    {
        $lock_key = self::LOCK_KEY_PREFIX . $key;
        if ($expire) {
            if (!$r = $mem->add($lock_key, 1, $expire)) {
                $try = (int) $expire * 1000000 / self::LOCK_SLEEP - 1;
                do {
                    usleep(self::LOCK_SLEEP);
                    $r = $mem->add($lock_key, 1, $expire);
                } while (!$r && --$try);
            }
        } else {
            $r = $mem->add($lock_key, 1, self::LOCK_EXPIRE);
        }
        return $r ? $lock_key : false;
    }

    /**
     * Redis缓存操作
     * @param key 缓存 key
     * @param data 待写入的数据：为 null 时表示读取缓存，可以是一个回调函数，只在需要写入时调用
     * @param expire 缓存时间：为假时表示不超时
     * @param lock 并发锁
     * @return 读取或写入的数据
     */
    public static function R(string $key, $data = null, int $expire = null, int $lock = 0)
    {
        $redis = self::Redis();
        isset($expire) || $expire = $GLOBALS['ZPHP_CONFIG']['REDIS']['expire'] ?? 600;
        if (null === $data) {
            $result = $redis->get($key);
            $result && $result = unserialize($result);
        } elseif ($lock) {
            $lock_key = self::LOCK_KEY_PREFIX . $key;
            if ($redis->set($lock_key, 1, ['nx', 'ex' => self::LOCK_EXPIRE])) {
                is_callable($data) && $data = $data() ?: '';
                $r = $expire ? $redis->setex($key, $expire, serialize($data)) : $redis->set($key, serialize($data));
                $redis->del($lock_key);
                $result = $r ? $data : false;
            } else {
                $result = $redis->get($key);
                $result && $result = unserialize($result);
            }
        } else {
            is_callable($data) && $data = $data() ?: '';
            $r = $expire ? $redis->setex($key, $expire, serialize($data)) : $redis->set($key, serialize($data));
            $result = $r ? $data : false;
        }
        return $result;
    }

    /**
     * Memcached缓存操作
     * @param key 缓存 key
     * @param data 待写入的数据：为 null 时表示读取缓存，可以是一个回调函数，只在需要写入时调用
     * @param expire 缓存时间：为假时表示不超时
     * @param lock 并发锁
     * @return 读取或写入的数据
     */
    public static function M($key, $data = null, $expire = null, $lock = 0)
    {
        $mem = self::Memcached();
        isset($expire) || $expire = $GLOBALS['ZPHP_CONFIG']['MEMCACHED']['expire'] ?? 600;
        if (null === $data) {
            $result = $mem->get($key);
            $result && $result = unserialize($result);
        } elseif ($lock) {
            $lock_key = self::LOCK_KEY_PREFIX . $key;
            if ($mem->add($lock_key, 1, self::LOCK_EXPIRE)) {
                is_callable($data) && $data = $data() ?: '';
                $r = $expire ? $mem->set($key, serialize($data), $expire) : $mem->set($key, serialize($data));
                $mem->delete($lock_key);
                $result = $r ? $data : false;
            } else {
                $result = $mem->get($key);
                $result && $result = unserialize($result);
            }
        } else {
            is_callable($data) && $data = $data() ?: '';
            $r = $expire ? $mem->set($key, serialize($data), $expire) : $mem->set($key, serialize($data));
            $result = $r ? $data : false;
        }
        return $result;
    }

    /**
     * 文件缓存操作
     * @param file 文件路径
     * @param data 待写入的数据：为 null 时表示读取缓存，可以是一个回调函数，只在需要写入时调用
     * @param expire 缓存时间：为假时表示不超时
     * @param lock 并发锁
     * @return 读取或写入的数据
     */
    public static function F($file, $data = null, $expire = 0, $lock = 0)
    {
        IsFullPath($file) || $file = P_CACHE_ . $file;
        if (null === $data) {
            if (is_file($file) && false !== ($str = ReadFileSH($file))) {
                $result = unserialize($str);
                if (isset($result['Z-PHP-CACHE-TIME-OUT'])) {
                    if (TIME < $result['Z-PHP-CACHE-TIME-OUT']) {
                        $result = $result['Z-PHP-CACHE-TDATA'];
                    } else {
                        unlink($file);
                        $result = false;
                    }
                }
            }
        } elseif ($lock) {
            $expire && $data = function () use ($data, $expire) {
                is_callable($data) && $data = $data();
                return [
                    'Z-PHP-CACHE-TDATA' => $data,
                    'Z-PHP-CACHE-TIME-OUT' => TIME + $expire,
                ];
            };
            $result = self::SetFileCache($file, $data);
            isset($result['Z-PHP-CACHE-TDATA']) && $result = $result['Z-PHP-CACHE-TDATA'];
        } else {
            is_callable($data) && $data = $data();
            $DATA = $expire ? ['Z-PHP-CACHE-TDATA' => $data, 'Z-PHP-CACHE-TIME-OUT' => TIME + $expire] : $data;
            false === file_put_contents($file, serialize($data), LOCK_EX) || $result = $data;
        }
        return $result ?? false;
    }

    /**
     * 写入文件缓存
     * @param file 文件路径
     * @param data 待写入的数据：可以是一个回调函数，只在需要写入时调用
     * @param export 数据为php代码
     * @return 写入的数据
     * 高并发时只有单个进程可以获取到锁，并写入文件；其它进程将等待写入完成后读取该文件数据并返回
     * 注意：windows 环境下如果同一秒内多次调用，只会写入一次！（不适合对时效要求很高[一秒内]的缓存）
     */
    public static function SetFileCache($file, $data, $export = false)
    {
        return 'WINDOWS' === ZPHP_OS ? self::setCacheWindows($file, $data, $export) : self::setCacheLinux($file, $data, $export);
    }
    private static function setCacheWindows($file, $data, $export)
    {
        $lock_path = P_CACHE . 'lock_file/';
        $lock_file = $lock_path . md5($file);
        file_exists($lock_path) || MakeDir($lock_path, 0755, true);
        if (!$h = fopen($lock_file, 'w')) {
            throw new \Exception('file can not write: ' . $lock_file);
        }
        if (flock($h, LOCK_EX)) {
            file_exists($dir = dirname($file)) || MakeDir($dir, 0755, true);
            clearstatcache(true, $file);
            if (!is_file($file) || filemtime($file) < TIME) {
                file_exists($dir = dirname($file)) || MakeDir($dir, 0755, true);
                is_callable($data) && $data = $data();
                if (false === file_put_contents($file, $export ? "<?php\nreturn " . var_export($data, true) . ';' : serialize($data), LOCK_EX)) {
                    throw new \Exception('file can not write: ' . $file);
                }
                flock($h, LOCK_UN);
                fclose($h);
                unlink($lock_file);
                return $data;
            }
            flock($h, LOCK_UN);
        }
        fclose($h);
        if ($export) {
            $data = require $file;
        } else {
            $data = ReadFileSH($file);
            $data && $data = unserialize($data);
        }
        return $data;
    }
    private static function setCacheLinux($file, $data, $export)
    {
        file_exists($dir = dirname($file)) || MakeDir($dir, 0755, true);
        if (!$h = fopen($file, 'w')) {
            throw new \Exception('file can not write: ' . $file);
        }
        if (flock($h, LOCK_EX | LOCK_NB)) {
            is_callable($data) && $data = $data();
            fwrite($h, $export ? "<?php\nreturn " . var_export($data, true) . ';' : serialize($data));
            flock($h, LOCK_UN);
            fclose($h);
        } elseif ($export) {
            $data = require $file;
        } else {
            $data = ReadFileSH($file);
            $data && $data = unserialize($data);
        }
        return $data;
    }
}
