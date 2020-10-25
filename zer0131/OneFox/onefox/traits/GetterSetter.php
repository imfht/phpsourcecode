<?php
/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc get set方法封装
 */

namespace onefox\traits;

trait GetterSetter {
    public function __call($methodName, $args) {
        if (preg_match('~^(set|get)([A-Z])(.*)$~', $methodName, $matches)) {
            $property = strtolower($matches[2]) . $matches[3];
            if (!property_exists($this, $property)) {
                throw new \Exception('Property ' . $property . ' not exists');
            }
            switch ($matches[1]) {
                case 'set':
                    return $this->_set($property, $args[0]);
                case 'get':
                    return $this->_get($property);
                case 'default':
                    throw new \Exception('Method ' . $methodName . ' not exists');
            }
        }
        return NULL;
    }

    public function _get($property) {
        return $this->$property;
    }

    public function _set($property, $value) {
        $this->$property = $value;
        return $this;
    }
}