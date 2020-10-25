<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 16-4-6
 * Time: 下午5:10
 */
class Category_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($id = null) {
        if ($id) {
            $query = $this->db->get_where('category', array('id' => $id));
            return $query->row_array();
        }

        $query = $this->db->get('category');
        return $query->result_array();
    }

    public function add($data) {
        $this->db->replace('category', $data);
    }

}