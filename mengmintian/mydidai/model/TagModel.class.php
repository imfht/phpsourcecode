<?php

class TagModel extends Model {

    private $id;
    private $name;
    private $info;
    private $sort;
    private $is_show;
    private $pid;


    
    
    public function __set($_key, $_value) {
        $this->$_key = $_value;
    }

    //拦截器(__get)
    public function __get($_key) {
        return $this->$_key;
    }
    
    
    //首页显示Tag
    public function IndexTag() {
        $_sql = "SELECT id,name FROM my_tag LIMIT 15";
        return $this->getAll($_sql);
    }
}