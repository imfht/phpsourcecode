<?php defined('BASEPATH') OR exit('No direct script access allowed');

class role_user_model extends MY_Model {
    const TB_ROLE_USERS = CIPLUS_DB_PREFIX . 'role_user';

    const TB_USER = CIPLUS_DB_PREFIX . 'user';

    public function __construct() {
        parent::__construct();
    }

    /**
     * 获取带权限的用户列表
     * @param $num
     * @param $offset
     * @param $key
     * @param $value
     * @return array
     */
    public function users($num, $offset, $key = null, $value = null) {
        $select = 'tb1.id, tb1.account, tb1.email, tb1.phone, tb1.create_time, GROUP_CONCAT(tb2.role_key) as roles';
        $join = 'tb1.id = tb2.user_id';

        $select = str_replace(array('tb1', 'tb2'), array(self::TB_USER, self::TB_ROLE_USERS), $select);
        $join = str_replace(array('tb1', 'tb2'), array(self::TB_USER, self::TB_ROLE_USERS), $join);

        $this->db->select($select);
        $this->db->from(self::TB_USER);
        $this->db->join(self::TB_ROLE_USERS, $join, 'LEFT OUTER');
        if ($key) $this->db->like($key, $value);
        $this->db->limit($num, $offset);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function users_total($key, $value) {
        if ($key) $this->db->like($key, $value);
        return $this->db->count_all_results(self::TB_USER);
    }

}