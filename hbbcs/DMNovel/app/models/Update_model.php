<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 16-4-7
 * Time: 下午4:03
 */
class Update_model  extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    function get($num=null) {
        $this->db->order_by('time','DESC');
        if ($num) {
            $this->db->limit($num);
        }

        return $this->db->get('update')->result_array();
    }
}