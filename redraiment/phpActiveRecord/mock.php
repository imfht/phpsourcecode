<?php

class Mock {
    private $data = array();

    public function reset() {
        $this->data = array();
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
        return $this->data[$name];
    }

    public function each($fn) {
        foreach ($this->data as $name => $value) {
            $fn($name, $value);
        }
    }
}
