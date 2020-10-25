<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 17-1-6
 * Time: 上午9:41
 */
class Users_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    function get($id=null,$where=null,$select=null,$limit='',$offset=0,$order='id',$sort='ASC') {
        if ($where) $this->db->where($where);
        if ($select) $this->db->select($select);
        if ($limit) $this->db->limit($limit,$offset);
        if ($order) $this->db->order_by($order,$sort);
        if ($id) {
            return $this->db->get_where('users',['id'=>$id])->row_array();
        }

        return $this->db->get('users')->result_array();
    }

    function valid($username, $password = null) {
        if (!$password) {
            return json_encode(['error' => '密码未填写。'], JSON_UNESCAPED_UNICODE);
        }

        $expiration = time() - 600; // Two hour limit
        $this->db->where('captcha_time < ', $expiration)
            ->delete('captcha');

        $sql = 'SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?';
        $binds = array($_POST['captcha'], $this->input->ip_address(), $expiration);
        $query = $this->db->query($sql, $binds);
        $row = $query->row();

        $user = $this->db->where('name', $username)->limit(1)->get('users')->row_array();
        if ($row->count == 0)        {
            return json_encode(['error' => '验证码错误。'], JSON_UNESCAPED_UNICODE);
        }

        if (!$user) {
            return json_encode(['error' => '用户不存在。'], JSON_UNESCAPED_UNICODE);
        } else if ($user['password'] != md5($password)) {
            return json_encode(['error' => '密码不正确。'], JSON_UNESCAPED_UNICODE);
        } else {
            return $user;
        }
    }

}