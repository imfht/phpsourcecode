<?php

/**
 * Class base_model
 */
class base_model
{

    public $table;
    public $primary_key;
    public $has_format_row = false;

    /**
     * construct base_db
     *
     * @param $table       string table name
     * @param $primary_key int|mixed primary key
     */
    function __construct($table = '', $primary_key = 'id') {
        $this->table       = $table;
        $this->primary_key = $primary_key;
        // check format row method exists to process item
        $this->has_format_row = method_exists($this, 'format_row');
    }

    /**
     * update $data by $id
     *
     * @param $data
     * @param $id
     *
     * @return mixed
     */
    public function update($data, $id) {
        if (is_array($id)) {
            return DB::update($this->table, $data, $id);
        } else {
            return DB::update($this->table, $data, array($this->primary_key => $id));
        }
    }

    /**
     * insert data
     *
     * @param     $data
     * @param int $return_id
     *
     * @return mixed
     */
    public function insert($data, $return_id = 0) {
        return DB::insert($this->table, $data, $return_id);
    }

    /**
     * replace $data
     *
     * @param $data
     *
     * @return mixed
     */
    public function replace($data) {
        return DB::replace($this->table, $data);
    }

    /**
     * db select method
     *
     * @param        $where
     * @param int    $order
     * @param int    $perpage
     * @param int    $page
     * @param string $index
     *
     * @return mixed
     */
    public function select($where, $order = 0, $perpage = -1, $page = 1, $index = '') {
        $table = $this->table;
        if (is_array($where) && isset($where['fields'])) {
            $table .= ':' . (is_array($where['fields']) ? implode(',', $where['fields']) : $where['fields']);
            unset($where['fields']);
        }
        // process where
        $this->process_where($where);
        // define callback
        $res = DB::select($table, $where, $order, $perpage, $page, $index);
        return $res;
    }

    /**
     * get recode
     *
     * @param $id int|mixed id or id array
     *
     * @return array array or single record
     */
    public function get($id) {
        $perpage = 0;
        if (is_array($id)) {
            if (isset($id[0])) {
                $where[$this->primary_key] = array('IN', $id);
                $perpage                   = -1;
            } else {
                $where = $id;
            }
        } else {
            $where = array($this->primary_key => $id);
        }
        // process where
        $this->process_where($where);
        $res = DB::select($this->table, $where, 0, $perpage);
        return $res;
    }

    /**
     * delete record by id or id array
     *
     * @param $id
     *
     * @return int|mixed
     */
    public function delete($id) {
        if (is_array($id)) {
            if (isset($id[0])) {
                //batch delete
                foreach ($id as $_id) {
                    self::delete($_id);
                }
                return 1;
            } else {
                //delete by where
                return DB::delete($this->table, $id);
            }
        } else {
            return DB::delete($this->table, array($this->primary_key => $id));
        }
    }

    /**
     * process where
     *
     * @param $where
     */
    function process_where(&$where) {
        if (isset($where['callback'])) {
            $callback          = $where['callback'];
            $that              = $this;
            $where['callback'] = function (&$item) use ($callback, $that) {
                // 先执行 format row
                if ($that->has_format_row) {
                    $that->format_row($item);
                }
                // 回调方法
                $callback($item);
            };
        } else if ($this->has_format_row && !is_string($where)) {
            $that              = $this;
            $where['callback'] = function (&$item) use ($that) {
                // format row
                $that->format_row($item);
            };
        }
    }

    /**
     * format item
     * child create format_row to format item
     */
    // function format_row(&$res) {}

    /**
     * format item
     *
     * @param $res
     */
    function format(&$res) {
        if (!$this->has_format_row) {
            return false;
        }
        if (!$res) {
            return false;
        }
        if (isset($res[0]) || (is_array($res) && !isset($res['id']))) {
            foreach ($res as $index => &$val) {
                $this->format_row($val);
            }
        } else {
            // format
            $this->format_row($res);
        }
        return true;
    }

}

?>