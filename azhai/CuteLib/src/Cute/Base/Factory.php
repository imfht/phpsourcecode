<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Base;

use \Cute\Base\Storage;


/**
 * 对象工厂
 */
class Factory
{
    protected $storage = null;
    protected $objects = []; // 对象池

    /**
     * 构造函数
     */
    public function __construct(Storage& $storage)
    {
        $this->storage = $storage;
    }

    /**
     * 正规化类名
     */
    public function normalize($class)
    {
        return rtrim($class, '\\');
    }

    /**
     * 生产对象
     */
    public function create($name, $key = 'default')
    {
        $section = $this->storage->getSectionOnce($name);
        $class = $this->normalize($section->getItem('class'));
        $data = $section->getArray($key);
        if ($key !== 'default') {
            $data = array_merge($section->getArray('default'), $data);
        }
        if (class_exists($class)) {
            foreach ($data as $field => &$value) {
                if (starts_with($field, '@')) {
                    $value = $this->load(trim($field, '@'), $value);
                }
            }
            return exec_construct_array($class, array_values($data));
        }
    }

    /**
     * 重拾对象
     */
    public function load($name, $key = 'default')
    {
        if (!isset($this->objects[$name])) {
            $this->objects[$name] = [];
            if (!isset($this->objects[$name][$key])) {
                $this->objects[$name][$key] = $this->create($name, $key);
            }
        }
        return $this->objects[$name][$key];
    }
}
