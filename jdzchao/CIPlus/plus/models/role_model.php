<?php defined('BASEPATH') OR exit('No direct script access allowed');

class role_model extends MY_Model {
    const TB_ROLE = CIPLUS_DB_PREFIX . 'role';
    const TB_ROLE_USERS = CIPLUS_DB_PREFIX . 'role_user';
    const TB_ROLE_API = CIPLUS_DB_PREFIX . 'role_api';

    public function __construct() {
        parent::__construct();
    }

    /**
     * 全部角色
     * @return mixed
     */
    public function all() {
        return $this->result_all(self::TB_ROLE);
    }

    /**
     * 角色总数
     * @return mixed
     */
    public function total() {
        return $this->count(self::TB_ROLE);
    }

    /**
     * 更多角色
     * @param int $p
     * @param int $n
     * @return mixed
     */
    public function more($p = 1, $n = 10) {
        $this->db->order_by('id desc');
        return $this->result(self::TB_ROLE, $p, $n);
    }

    /**
     * 添加角色
     * @param $key
     * @param $name
     * @param string $description
     * @return mixed
     */
    public function add($key, $name, $description = "") {
        $data = array(
            'key' => $key,
            'name' => $name,
            'description' => $description,
        );
        return $this->insert(self::TB_ROLE, $data);
    }

    /**
     * 编辑角色
     * @param $id
     * @param $name
     * @param string $description
     * @return mixed
     */
    public function edit($id, $name, $description = "") {
        $data = array(
            'name' => $name,
            'description' => $description,
        );
        $where = array('id' => $id);
        return $this->update(self::TB_ROLE, $data, $where);
    }

    /**
     * 删除角色
     * @param $id
     * @return mixed
     */
    public function del($id) {
        if ($this->readonly($id)) return null;
        $where = array("id" => $id);
        return $this->delete(self::TB_ROLE, $where);
    }

    /**
     * 是否只读
     * @param $id
     * @return bool
     */
    public function readonly($id) {
        $this->db->where('id', $id);
        $res = $this->row(self::TB_ROLE);
        return $res['readonly'] == 1;
    }

    /**
     * 获取用户权限数组
     * @param $user_id
     * @return array
     */
    public function getRoles($user_id) {
        $this->db->where('user_id', $user_id);
        $res = $this->result_all(self::TB_ROLE_USERS);
        if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
            $roles = array_column($res, 'role_key');
        } else {
            $roles = array();
            foreach ($roles as $item) {
                array_push($item['role_key']);
            }
        }
        return $roles;
    }

    /**
     * 验证接口权限
     * @param $user_id
     * @param $api_key
     * @return bool
     */
    public function verify($user_id, $api_key) {
        $roles = $this->getRoles($user_id);
        if (in_array('admin', $roles)) return true;

        $this->db->where('api_key', $api_key);
        $res = $this->result_all(self::TB_ROLE_API);

        $arr = array_intersect_key($roles, $res);
        return count($arr) > 0;
    }


}