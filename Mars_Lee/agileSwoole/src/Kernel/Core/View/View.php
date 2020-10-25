<?php


namespace Kernel\Core\View;


use Kernel\AgileCore;
use Kernel\Core\Exception\ErrorCode;
use Kernel\Core\Exception\FileNotFoundException;

class View
{
    protected $path;
    protected $data;

    /**
     * View constructor.
     * @param string $path
     * @param array $data
     * @throws \Exception
     */
    public function __construct(string $path, array $data)
    {
        if(!is_file($path)) {
            $default = rtrim(APP_PATH,'/').'/View/'.ltrim($path,'/');
            if(!is_file($default)) {
                $config = AgileCore::getInstance()->get('config')->get('views');
                $path = rtrim($config['path'],'/') .'/'. ltrim($path, '/');
                if (!is_file($path)) {
                    throw new FileNotFoundException($path . ' not found', ErrorCode::FILE_NOT_FOUND);
                }
            }else{
                $path = $default;
            }
        }

        $this->path = $path;
        $this->data = $data;
    }

    public function display() : string
    {
        extract($this->data);
        ob_start();
        include($this->path);
        $content = ob_get_contents();
        @ob_end_clean();
        return $content;
    }

    public function __destruct()
    {
        $this->path = '';
        $this->data = [];
    }

    /**
     * @param string $path
     * @param array $data
     * @return View
     * @throws \Exception
     */
    public static function render(string $path, array $data = [])
    {
        return new self($path, $data);
    }
}