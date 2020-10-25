<?php declare (strict_types = 1);
namespace msqphp\main\view\component;

final class Language
{
    private $config   = [];
    private $language = '';

    public function __construct(array $config)
    {
        // 获取当前语言
        is_dir($config['path']) || $this->exception('语言存放目录不存在,无法开启视图多语支持');

        $this->config['path'] = realpath($config['path']) . DIRECTORY_SEPARATOR;

        $this->language = defined('__LANGUAGE__') ? __LANGUAGE__ : $config['default'];
    }

    // 抛出异常
    private function exception(string $message): void
    {
        throw new ViewComponentException($message);
    }

    // 获取语言
    public function get(): string
    {

        return $this->language;
    }
    // 设置语言
    public function set(string $language): void
    {
        in_array($this->config['allowed']) || $this->exception('视图多语言允许列表中不包括该主题:' . $language);

        $this->language = $language;
    }
    /**
     * 获取对应的语言数据
     * @param   string  $file_name  文件名称
     * @return  array
     */
    public function getData(string $file_name, string $group): array
    {
        $file = $this->config['path'] . $this->language . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR . $file_name . '.php';

        is_file($file) || $file = $this->config['path'] . $this->config['default'] . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR . $file_name . '.php';

        return is_file($file) ? require $file : [];
    }
}
