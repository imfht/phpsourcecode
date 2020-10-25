<?php declare (strict_types = 1);
namespace msqphp\main\cache\handlers;

use msqphp\base;

final class File implements CacheHandlerInterface
{
    // 配置参数
    private $config = [
        // 路径
        'path'      => '',
        // 后缀
        'extension' => '',
        // 深度
        'deep'      => 1,
        // 最大文件缓存数
        'length'    => 0,
    ];

    // 初始化
    public function __construct(array $config)
    {
        $config = array_merge($this->config, $config);

        is_dir($config['path']) || $this->exception($config['path'] . '缓存路径不存在');

        (!is_writable($config['path']) || !is_readable($config['path'])) && $this->exception($config['path'] . '缓存路径不可读写');

        $config['path'] = realpath($config['path']) . DIRECTORY_SEPARATOR;

        $this->config = $config;
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

    // 是否存在
    public function available(string $key): bool
    {
        // 获取文件路径
        $file = $this->filename($key);
        // 文件不存在返回false
        if (!is_file($file)) {
            return false;
        }
        // 读取前十个字符, 如果大于现在时间, 则过期
        try {
            // 是否为空
            if (time() > (int) base\file\File::read($file, 10)) {
                base\file\File::delete($file);
                return false;
            } else {
                return true;
            }
        } catch (base\file\FileException $e) {
            return false;
        }
    }

    // 获取
    public function get(string $key)
    {
        // 得到内容
        try {
            $value = base\file\File::get($this->filename($key));
        } catch (base\file\FileException $e) {
            $this->exception($e->getMessage());
        }
        // 去除前十个字符（过期时间）
        return unserialize(substr($value, 10));
    }

    // 设置
    public function set(string $key, $value, int $expire): void
    {
        // 值:过期时间 . 转义后的值

        $value = (string) (time() + $expire) . serialize($value);

        // 存储
        try {

            base\file\File::write($this->filename($key), $value, true);

            // 如果限制了最大储存数, 调用队列
            $this->config['length'] > 0 && $this->queue($key);

        } catch (base\file\FileException $e) {
            $this->exception($e->getMessage());
        }
    }
    // 递增
    public function increment(string $key, int $offset): int
    {
        try {
            // 获取文件路径
            $file = $this->filename($key);
            // 判断文件情况
            is_file($file) || $this->exception($file . '缓存文件不存在');
            // 获取内容
            $content = base\file\File::get($file);
            // 过期时间
            $expire = (int) substr($content, 0, 10);

            $num = time() > $expire ? 0 : (int) unserialize(substr($content, 10));
            $num += $offset;

            base\file\File::write($file, (string) $expire . serialize($num), true);

            return $num;
        } catch (base\file\FileException $e) {
            $this->exception($e->getMessage());
        }
    }
    // 递减
    public function decrement(string $key, int $offset): int
    {
        return $this->increment($key, 0 - $offset);
    }
    // 删除缓存
    public function delete(string $key): void
    {
        // 获取文件路径
        try {
            base\file\File::delete($this->filename($key), true);
        } catch (base\file\FileException $e) {
            $this->exception($e->getMessage());
        }
    }
    // 清空所有缓存
    public function clear(): void
    {
        try {
            base\dir\Dir::deleteAllFileByType($this->config['path'], $this->config['extension']);
        } catch (base\file\FileException $e) {
            $this->exception($e->getMessage());
        }
    }
    // 缓存队列整理
    private function queue($key)
    {

        // 获取缓存队列文件名
        $queue_file = $this->config['path'] . 'cacheQueue.php';

        // 如果不存在
        $queue = is_file($queue_file) ? require $queue_file : [];

        // 如果未找到则添加
        false === array_search($key, $queue) && array_push($queue, $key);

        try {
            // 如果队列长度大于配置长度
            if (count($queue) > $this->config['length']) {
                // 移除第一个
                $old_key = array_shift($queue);
                // 删除对应文件
                base\file\File::delete($this->filename($old_key), true);
            }
            // 重新写入
            base\file\File::write($queue_file, '<?php return ' . var_export($queue, true) . ';', true);
        } catch (base\file\FileException $e) {
            $this->exception($e->getMessage());
        }
    }
    /**
     * 得到缓存文件名
     *
     * @param  string $key 键
     *
     * @return string
     */
    private function filename(string $key): string
    {
        $name = md5($key);

        $dir = $this->config['path'];
        // 深度
        $deep = $this->config['deep'];

        for ($i = 0; $i < $deep; ++$i) {
            $dir .= $name[$i] . DIRECTORY_SEPARATOR;
            // 目录不存在则创建
            is_dir($dir) || base\dir\Dir::make($dir, true, 0755);
        }

        // 目录.md5值.扩展名
        return $dir . $name . $this->config['extension'];
    }
}
