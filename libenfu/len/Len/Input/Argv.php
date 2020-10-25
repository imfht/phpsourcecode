<?php

namespace Input;

class Argv implements InputBase
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
     * argv constructor.
     */
    public function __construct()
    {
        IS_CLI AND $this->argvTool();

    }

    private function argvTool()
    {
        $_argv = array_slice($_SERVER['argv'], 1);
        $params = array();
        $params['control'] = empty($_argv[0]) ? null : $_argv[0];
        $params['action'] = empty($_argv[1]) ? null : $_argv[1];
        $argv_list = array_slice($_argv, 2);
        foreach ($argv_list as $arg) {
            $arg = explode('=', $arg);
            if (count($arg) < 2) {
                continue;
            }
            $params[$arg[0]] = $arg[1];
        }

        $this->setParams($params);
    }
}