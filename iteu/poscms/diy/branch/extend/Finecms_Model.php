<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class CI_Model {

    public $ci;
    public $link;

    public function __construct()
    {
        $this->ci = & get_instance();
        $this->link = $this->ci->db;
    }

    /**
     * __get magic
     *
     * Allows models to access CI's loaded classes using the same
     * syntax as controllers.
     *
     * @param	string	$key
     */
    public function __get($key)
    {
        // Debugging note:
        //	If you're here because you're getting an error message
        //	saying 'Undefined Property: system/core/Model.php', it's
        //	most likely a typo in your model code.
        return $this->ci->$key;
    }
}