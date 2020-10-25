<?php defined('BASEPATH') OR exit('No direct script access allowed');

class role_api_model extends MY_Model {
    const TB_ROLE_API = CIPLUS_DB_PREFIX . 'role_api';

    public function __construct() {
        parent::__construct();
    }

    /**
     * 返回角色的全部接口
     * @param $role_key
     * @return mixed
     */
    public function all($role_key) {
        $re = $this->result_all(self::TB_ROLE_API,
            array(
                'role_key' => $role_key
            )
        );
        return $re;
    }

    public function edit($role, array $dict = array()) {
        $data = array();
        foreach ($dict as $k => $v) {
            array_push($data, array(
                "role_key" => $role,
                "api_key" => $v
            ));
        }
        return $this->db->insert_batch(self::TB_ROLE_API, $data);
    }

    public function clean($role) {
        $this->db->where('role_key', $role);
        return $this->delete(self::TB_ROLE_API);
    }

}