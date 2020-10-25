<?php declare (strict_types = 1);
namespace msqphp\test\core\config;

final class ConfigTest extends \msqphp\test\Test
{
    public function testStart(): void
    {
        // 获得当前配置路径
        $config_path = \msqphp\Environment::getPath('config');
        // 设置配置路径为测试路径
        \msqphp\Environment::setPath(['config' => __DIR__ . DIRECTORY_SEPARATOR . 'resources']);
        $this->class('\msqphp\core\config\Config');
        \msqphp\core\config\Config::init();
        // 测试该类
        $this->testThis();
        // 还原配置路径
        \msqphp\Environment::setPath(['config' => $config_path]);
        \msqphp\core\config\Config::init();
    }

    public function testConfig(): void
    {
        // 如果有缓存,会强制加载缓存的配置文件,无法测试,直接返回
        if (HAS_CACHE) {
            return;
        }
        $this->clear();
        $this->method('get');

        $this->args('test.test')
            ->result('test')
            ->test();

        $this->args('test.test_array.a')
            ->result('A')
            ->test();

        $this->method('set')
            ->args('test.test_array.a', 'B')
            ->result(null)
            ->test();

        $this->method('get')
            ->args('test.test_array.a')
            ->result('B')
            ->test();
    }
}
