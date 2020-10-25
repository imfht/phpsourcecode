<?php

namespace Input;

class Put implements InputBase
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
     * put constructor.
     */
    public function __construct()
    {
        parse_str(file_get_contents('php://input'), $_PUT);

        $this->setParams($_PUT);
    }
}