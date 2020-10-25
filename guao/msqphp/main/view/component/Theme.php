<?php declare(strict_types = 1);
namespace msqphp\main\view\component;

final class Theme
{
    private $theme;
    private $config;

    // 初始化主题支持
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->theme = defined('__THEME__') ? __THEME__ : $this->config['default'];
    }

    // 抛出异常
    private function exception(string $message) : void
    {
        throw new ViewComponentException($message);
    }

    // 获取主题
    public function get() : string
    {
        return $this->theme;
    }

    // 设置主题
    public function set(string $theme) : void
    {
        in_array($this->config['allowed']) || $this->exception('视图多主题允许列表中不包括该主题:'.$theme);

        $this->theme = $theme;
    }
}