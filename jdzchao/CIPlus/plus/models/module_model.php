<?php defined('BASEPATH') OR exit('No direct script access allowed');

class module_model extends MY_Model {
    const TB_MODULE = CIPLUS_DB_PREFIX . 'module';


    public function __construct() {
        parent::__construct();
    }

    /**
     * 全部模块
     * @return mixed
     */
    public function all() {
        return $this->result_all(self::TB_MODULE);
    }

    /**
     * 模块总数
     * @return mixed
     */
    public function total() {
        return $this->count(self::TB_MODULE);
    }

    /**
     * 模块列表
     * @param int $p
     * @param int $n
     * @return mixed
     */
    public function more($p = 1, $n = 10) {
        $this->db->order_by('id desc');
        return $this->result(self::TB_MODULE, $p, $n);
    }

    /**
     * 添加模块
     * @param $key
     * @param $name
     * @param int $parent_id
     * @return mixed
     */
    public function add($key, $name, $parent_id = 0) {
        $data = array(
            'key' => $key,
            'name' => $name,
            'parent_id' => $parent_id,
        );
        return $this->insert(self::TB_MODULE, $data);
    }

    /**
     * 修改模块
     * @param $id
     * @param $name
     * @param int $parent_id
     * @return mixed
     */
    public function edit($id, $name, $parent_id = 0) {
        $data = array(
            'name' => $name,
            'parent_id' => $parent_id
        );
        $where = array('id' => $id);
        return $this->update(self::TB_MODULE, $data, $where);
    }

    /**
     * 删除模块
     * @param $id
     * @return mixed
     */
    public function del($id) {
        $where = array("id" => $id);
        return $this->delete(self::TB_MODULE, $where);
    }

    /**
     * 获取单个模块
     * @param $id
     * @return mixed
     */
    public function get($id) {
        $where = array("id" => $id);
        return $this->row(self::TB_MODULE, $where);
    }

}