<?php declare (strict_types = 1);
namespace msqphp\test\core\cache;

use msqphp\base;
use msqphp\main;

class CacheTest extends \msqphp\test\Test
{
    public function testStart(): void
    {
        $this->init();
        // 获得当前cache配置
        $cache_config = \msqphp\core\config\Config::get('cache');
        // 设置cache配置为测试配置
        \msqphp\core\config\Config::set('cache', [
            // 是否允许多缓存处理器
            'multi'           => true,
            // 缓存处理器支持列表
            'sports'          => ['File', 'Memcached'],
            // 默认处理器
            'default_handler' => 'File',
            // 缓存前缀(影响全部)
            'prefix'          => 'msq_',
            // 默认过期时间(影响全部)
            'expire'          => 3600,
            // 处理器配置
            'handlers_config' => [
                /*
                通用配置
                'length'   =>  最多储存多少个缓存.即启用缓存队列,0则无限
                 */
                'File' => [
                    // 路径
                    'path'      => __DIR__ . '/storage/cache',
                    // 后缀
                    'extension' => '.cache',
                    // 深度
                    'deep'      => 0,
                    // 最大文件缓存数
                    'length'    => 0,
                    // 数据是否压缩
                    'compress'  => false,
                ],
            ],
        ]);
        // 清空测试目录,以防上次测试失败,留有残余
        base\dir\Dir::empty(__DIR__ . '/storage/cache');
        // 赋值一个新的缓存对象
        $this->object(new main\cache\Cache());
        $this->testThis($this);
        // 还原cache配置
        \msqphp\core\config\Config::set('cache', $cache_config);
    }
    public function testFile(): void
    {
        // 如果无缓存,则无法保存文件,无法进行测试.
        if (!HAS_CACHE) {
            return;
        }
        $this->clear();
        $this->chain([
            ['init', 'File'],
            ['key', 'test'],
            ['exists'],
        ])->result(false)->test();
        $this->chain([
            ['value', 1],
            ['set'],
        ])->result(null)->test();
        $this->method('exists')->args(null)->result(true)->test();
        $this->method('get')->args()->result(1)->test();
        $this->method('increment')->args()->result(2)->test();
        $this->method('get')->args()->result(2)->test();
        $this->method('decrement')->args()->result(1)->test();
        $this->method('get')->args()->result(1)->test();
        $this->method('inc')->args()->result(2)->test();
        $this->method('get')->args()->result(2)->test();
        $this->method('dec')->args()->result(1)->test();
        $this->method('exists')->args()->result(true)->test();
        $this->method('delete')->args()->result(null)->test();
        $this->method('exists')->args()->result(false)->test();
    }
}
