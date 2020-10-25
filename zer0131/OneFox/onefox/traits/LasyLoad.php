<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 延迟初始化工具类
 */

namespace onefox\traits;

trait LasyLoad {

    private $__objArr__ = [];

    public function bind($valueName, $className) {
        $this->__objArr__[$valueName] = $className;
    }

    public function __get($valueName) {
        if (!isset($this->__objArr__[$valueName])  || !is_string($this->__objArr__[$valueName]) || !class_exists($this->__objArr__[$valueName])) {
            return null;
        }
        $this->$valueName = new $this->__objArr__[$valueName];
        return $this->$valueName;
    }
}