<?php defined('BASEPATH') OR exit('No direct script access allowed');

class api_model extends MY_Model {
    const TB_API = CIPLUS_DB_PREFIX . 'api';


    public function __construct() {
        parent::__construct();
    }

    /**
     * 全部接口
     * @return mixed
     */
    public function all() {
        return $this->result_all(self::TB_API);
    }

    /**
     * 接口数
     * @param null $title
     * @return mixed
     */
    public function total($title = null) {
        if ($title) $this->db->like('title', $title);
        return $this->count(self::TB_API);
    }

    /**
     * 接口列表
     * @param null $title
     * @param int $p
     * @param int $n
     * @return mixed
     */
    public function more($title = null, $p = 1, $n = 10) {
        if ($title) $this->db->like('title', $title);
        $this->db->order_by('id desc');
        return $this->result(self::TB_API, $p, $n);
    }

    /**
     * 添加接口
     * @param $title
     * @param $path
     * @param array $required
     * @param array $optional
     * @param string $method
     * @param string $module
     * @param int $validated
     * @return mixed
     */
    public function add($title, $path, $required = array(), $optional = array(), $method = 'request', $module = "", $validated = 1) {
        $data = array(
            'key' => unique_code(),
            'title' => $title,
            'path' => $path,
            'required' => $required,
            'optional' => $optional,
            'method' => $method,
            'module' => $module,
            'validated' => $validated
        );
        return $this->insert(self::TB_API, $data);
    }

    /**
     * 修改接口
     * @param $id
     * @param $title
     * @param $path
     * @param array $required
     * @param array $optional
     * @param string $method
     * @param string $module
     * @param int $validated
     * @return mixed
     */
    public function edit($id, $title, $path, $required = array(), $optional = array(), $method = 'request', $module = "", $validated = 1) {
        $data = array(
            'key' => unique_code(),
            'title' => $title,
            'path' => $path,
            'required' => $required,
            'optional' => $optional,
            'method' => $method,
            'module' => $module,
            'validated' => $validated
        );
        $where = array('id' => $id);
        return $this->update(self::TB_API, $data, $where);
    }


    /**
     * 删除（冻结）接口
     * @param $id
     * @return mixed
     */
    public function del($id) {
        $data = array("usable" => 0);
        $where = array("id" => $id);
        return $this->update(self::TB_API, $data, $where);
    }

    /**
     * 恢复接口
     * @param $id
     * @return mixed
     */
    public function revive($id) {
        $data = array("usable" => 1);
        $where = array("id" => $id);
        return $this->update(self::TB_API, $data, $where);
    }

    /**
     * 通过路径搜索接口
     * @param $path
     * @return mixed
     */
    public function by_path($path) {
        $this->db->where('usable', 1);
        return $this->row(self::TB_API, ['path' => $path]);
    }

    /**
     * 清理 module key
     * @param $module
     * @return mixed
     */
    public function remove_module($module) {
        $data = array(
            'module' => "",
        );
        $where = array('module' => $module);
        return $this->update(self::TB_API, $data, $where);
    }
}