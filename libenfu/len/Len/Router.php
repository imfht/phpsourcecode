<?php

class Router
{
    public $control = '';
    public $action = '';

    public static function instance($config)
    {
        return new self($config);
    }

    private function __construct($config)
    {
        if (isset($_SERVER['REQUEST_URI']) && '/' != $_SERVER['REQUEST_URI']) {
            $path_info_string = str_replace('/index.php', '', $_SERVER['REQUEST_URI']);
            $path_info_arr = explode('/', trim($path_info_string, '/'));
            if (isset($path_info_arr[0])) {
                $this->control = ucfirst($path_info_arr[0]);
            }
            if (isset($path_info_arr[1])) {
                $this->action = $path_info_arr[1];
            }
        }

        $this->control = !empty($this->control) ? $this->control : ucfirst($config['default_control']);
        $this->action = !empty($this->action) ? $this->action : $config['default_action'];
    }
}