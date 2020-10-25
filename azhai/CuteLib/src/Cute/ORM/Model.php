<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM;


/**
 * 数据模型
 */
class Model
{
    protected static $_fields = [];

    public function getBehaviors()
    {
        return [];
    }

    public static function getTable()
    {
        return '%s';
    }

    public static function getPKeys()
    {
        return [];
    }

    public function getID($i = 0)
    {
        if ($pkeys = $this->getPKeys()) {
            $pkey = $pkeys[$i];
            return $this->$pkey;
        }
    }

    public function setID($id)
    {
        if ($pkeys = $this->getPKeys()) {
            foreach ($pkeys as $i => $pkey) {
                $this->$pkey = func_get_arg($i);
            }
        }
        return $this;
    }

    public function getFields()
    {
        $table = $this->getTable();
        if (!isset(self::$_fields[$table])) {
            $fields = get_object_vars($this);
            foreach ($fields as $name => $default) {
                if (starts_with($name, '_')) {
                    unset($fields[$name]);
                }
            }
            self::$_fields[$table] = &$fields;
        }
        return self::$_fields[$table];
    }

    public function isExists()
    {
        $id = $this->getID();
        return $id !== 0 && !is_null($id);
    }

    public function toArray(array $includes = null, array $excludes = array())
    {
        if (!empty($includes)) {
            foreach ($includes as $key) {
                if (property_exists($this, $key)) {
                    $data[$key] = $this->$key;
                } else {
                    $data[$key] = null;
                }
            }
        } else {
            $data = get_object_vars($this);
            foreach ($data as $key => $value) {
                if (starts_with($key, '_') || in_array($key, $excludes)) {
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }
}
