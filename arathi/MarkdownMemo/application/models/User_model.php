<?php
class User_model extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    public function login($username, $hashedPWD){
        //$query = $this->db->get_where('users', array('username' => $username, 'password' => $hashedPWD));
        $this->db->select('uid, username, group')
                ->where('username', $username)
                ->where('password', $hashedPWD)
                ->limit(1);
        $query = $this->db->get('users');
        return $query->result_array();
        //return $query->record_array();
    }
}
