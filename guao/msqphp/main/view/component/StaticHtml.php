<?php declare(strict_types = 1);
namespace msqphp\main\view\component;

use msqphp\core\route\Route;

final class StaticHtml
{
    private $path;
    private $content;
    public function __construct(array $config)
    {

        $this->path = Route::getStaticPath();
        $this->expire = $config['expire'];
    }
    public function addContent(string $content) : void
    {
        $this->content .= $content;
    }
    // 静态页面写入
    public function writeHtml() : void
    {
        Route::writeStaticFile($this->path, $this->content, $this->expire);
    }
    public function __destruct()
    {
        $this->writeHtml();
    }
}