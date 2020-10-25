<?php

/**
 * 自定义一般Model,用于取代CI_Model进行使用
 */
class MY_Model extends CI_Model
{
    protected $time = null;
    protected $now = null;

    public function __construct()
    {
        parent::__construct();
        $this->time = time();
        $this->now = date('Y-m-d H:i:s', $this->time);
        $this->load->database();
    }
}
