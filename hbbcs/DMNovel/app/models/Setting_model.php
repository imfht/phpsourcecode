<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 15-10-23
 * Time: 下午4:18
 *
 * @get_setting Get_setting $Setting_model
 */
class Setting_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($title = null, $id = null) {
        if ($title) {
            $query = $this->db->get_where('setting', array('title' => $title))->row_array();
            return $query['value'];
        }

        if ($id) {
            $query = $this->db->get_where('setting', array('title' => $title));
            return $query->row_array();
        }

        return $this->db->get('setting')->result_array();
    }

    function get_setting($id = null) {
        if ($id == null) {
            return $this->db->get('setting')->result_array();
        } else {
            return $this->db->get_where('setting',array('id'=>$id))->row_array();
        }
    }

    function get_value($name) {
        $setting = $this->db->get('setting',array('name'=>$name))->result_array();
        return $setting[0]['value'];
    }

    function get_setting_num() {
        return $this->db->count_all('setting');
    }

    function get_page($page=0) {
        $per_page=config_item('per_page');
        $this->db->limit($per_page,$page);
        return $this->db->get('setting')->result_array();
    }

}