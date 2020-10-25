<?php

namespace Input;

class Get implements InputBase
{
    /**
     * @var array
     */
    private $params = array();

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->params;
    }

    /**
     * @param $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @param $item_name
     * @param null $default_param
     * @return null|mixed
     */
    public function getOne($item_name, $default_param = null)
    {
        if (!empty($this->params[$item_name])) {
            return $this->params[$item_name];
        }

        return $default_param;
    }

    /**
     * @return array
     */
    public function getMany()
    {
        $field_list = func_get_args();
        if (empty($field_list)) {
            return array();
        }

        $list = array();
        foreach ($field_list as $field) {
            if (isset($this->params[$field])) {
                $list[$field] = $this->params[$field];
            }
        }

        return $list;
    }

    /**
     * get constructor.
     */
    public function __construct()
    {
        IS_CLI OR $this->pathInfoStringTool();
    }

    private function pathInfoStringTool()
    {
        if (isset($_SERVER['REQUEST_URI']) && '/' != $_SERVER['REQUEST_URI']) {
            $path_info_string = str_replace('/index.php', '', $_SERVER['REQUEST_URI']);
            $path_info_arr = explode('/', trim($path_info_string, '/'));
            if (isset($path_info_arr[1])) {
                $_GET['control'] = $path_info_arr[0];
                $_GET['action'] = $path_info_arr[1];
                $path_info_arr = array_slice($path_info_arr, 2);
                $count = count($path_info_arr);
                for ($i = 0; $i <= $count; $i += 2) {
                    if (isset($path_info_arr[$i + 1])) {
                        $_GET[$path_info_arr[$i]] = $path_info_arr[$i + 1];
                    }
                }
            }
        }

        $this->setParams($_GET);
    }
}