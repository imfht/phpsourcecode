<?php

namespace fluiex\db;

use fluiex\Object;
use fluiex\DB;
use fluiex\db\Exception;

/**
 * 基于db单表的DAO对象
 */
class Table extends Object
{

    public $data = array();
    public $methods = array();
    protected $name;
    protected $pk;
    protected $pre_cache_key;
    protected $cache_ttl;
    protected $allowmem;

    public function __construct($para = array())
    {
        if (!empty($para)) {
            $this->name = $para['table'];
            $this->pk = $para['pk'];
        }
        if (isset($this->pre_cache_key) && (($ttl = getglobal('setting/memory/' . $this->name)) !== null || ($ttl = $this->cache_ttl) !== null) && memory('check')) {
            $this->cache_ttl = $ttl;
            $this->allowmem = true;
        }
        $this->_init_extend();
        parent::__construct();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        return $this->name = $name;
    }

    public function count()
    {
        $count = (int) DB::result_first("SELECT count(*) FROM " . DB::table($this->name));
        return $count;
    }

    public function update($val, $data, $unbuffered = false, $low_priority = false)
    {
        if (isset($val) && !empty($data) && is_array($data)) {
            $this->checkpk();
            $ret = DB::update($this->name, $data, DB::field($this->pk, $val), $unbuffered, $low_priority);
            foreach ((array) $val as $id) {
                $this->update_cache($id, $data);
            }
            return $ret;
        }
        return !$unbuffered ? 0 : false;
    }

    public function delete($val, $unbuffered = false)
    {
        $ret = false;
        if (isset($val)) {
            $this->checkpk();
            $ret = DB::delete($this->name, DB::field($this->pk, $val), null, $unbuffered);
            $this->clear_cache($val);
        }
        return $ret;
    }

    public function truncate()
    {
        DB::query("TRUNCATE " . DB::table($this->name));
    }

    public function insert($data, $return_insert_id = false, $replace = false, $silent = false)
    {
        return DB::insert($this->name, $data, $return_insert_id, $replace, $silent);
    }

    public function checkpk()
    {
        if (!$this->pk) {
            throw new Exception('Table ' . $this->name . ' has not PRIMARY KEY defined');
        }
    }

    public function fetch($id, $force_from_db = false)
    {
        $data = array();
        if (!empty($id)) {
            if ($force_from_db || ($data = $this->fetch_cache($id)) === false) {
                $data = DB::fetch_first('SELECT * FROM ' . DB::table($this->name) . ' WHERE ' . DB::field($this->pk, $id));
                if (!empty($data))
                    $this->store_cache($id, $data);
            }
        }
        return $data;
    }

    public function fetch_all($ids, $force_from_db = false)
    {
        $data = array();
        if (!empty($ids)) {
            if ($force_from_db || ($data = $this->fetch_cache($ids)) === false || count($ids) != count($data)) {
                if (is_array($data) && !empty($data)) {
                    $ids = array_diff($ids, array_keys($data));
                }
                if ($data === false)
                    $data = array();
                if (!empty($ids)) {
                    $query = DB::query('SELECT * FROM ' . DB::table($this->name) . ' WHERE ' . DB::field($this->pk, $ids));
                    while ($value = DB::fetch($query)) {
                        $data[$value[$this->pk]] = $value;
                        $this->store_cache($value[$this->pk], $value);
                    }
                }
            }
        }
        return $data;
    }

    public function fetch_all_field()
    {
        $data = false;
        $query = DB::query('SHOW FIELDS FROM ' . DB::table($this->name), '', 'SILENT');
        if ($query) {
            $data = array();
            while ($value = DB::fetch($query)) {
                $data[$value['Field']] = $value;
            }
        }
        return $data;
    }

    public function range($start = 0, $limit = 0, $sort = '')
    {
        if ($sort) {
            $this->checkpk();
        }
        return DB::fetch_all('SELECT * FROM ' . DB::table($this->name) . ($sort ? ' ORDER BY ' . DB::order($this->pk, $sort) : '') . DB::limit($start, $limit), null, $this->pk ? $this->pk : '');
    }

    public function optimize()
    {
        DB::query('OPTIMIZE TABLE ' . DB::table($this->name), 'SILENT');
    }

    public function fetch_cache($ids, $pre_cache_key = null)
    {
        $data = false;
        if ($this->allowmem) {
            if ($pre_cache_key === null)
                $pre_cache_key = $this->pre_cache_key;
            $data = memory('get', $ids, $pre_cache_key);
        }
        return $data;
    }

    public function store_cache($id, $data, $cache_ttl = null, $pre_cache_key = null)
    {
        $ret = false;
        if ($this->allowmem) {
            if ($pre_cache_key === null)
                $pre_cache_key = $this->pre_cache_key;
            if ($cache_ttl === null)
                $cache_ttl = $this->cache_ttl;
            $ret = memory('set', $id, $data, $cache_ttl, $pre_cache_key);
        }
        return $ret;
    }

    public function clear_cache($ids, $pre_cache_key = null)
    {
        $ret = false;
        if ($this->allowmem) {
            if ($pre_cache_key === null)
                $pre_cache_key = $this->pre_cache_key;
            $ret = memory('rm', $ids, $pre_cache_key);
        }
        return $ret;
    }

    public function update_cache($id, $data, $cache_ttl = null, $pre_cache_key = null)
    {
        $ret = false;
        if ($this->allowmem) {
            if ($pre_cache_key === null)
                $pre_cache_key = $this->pre_cache_key;
            if ($cache_ttl === null)
                $cache_ttl = $this->cache_ttl;
            if (($_data = memory('get', $id, $pre_cache_key)) !== false) {
                $ret = $this->store_cache($id, array_merge($_data, $data), $cache_ttl, $pre_cache_key);
            }
        }
        return $ret;
    }

    public function update_batch_cache($ids, $data, $cache_ttl = null, $pre_cache_key = null)
    {
        $ret = false;
        if ($this->allowmem) {
            if ($pre_cache_key === null)
                $pre_cache_key = $this->pre_cache_key;
            if ($cache_ttl === null)
                $cache_ttl = $this->cache_ttl;
            if (($_data = memory('get', $ids, $pre_cache_key)) !== false) {
                foreach ($_data as $id => $value) {
                    $ret = $this->store_cache($id, array_merge($value, $data), $cache_ttl, $pre_cache_key);
                }
            }
        }
        return $ret;
    }

    public function reset_cache($ids, $pre_cache_key = null)
    {
        $ret = false;
        if ($this->allowmem) {
            $keys = array();
            if (($cache_data = $this->fetch_cache($ids, $pre_cache_key)) !== false) {
                $keys = array_intersect(array_keys($cache_data), $ids);
                unset($cache_data);
            }
            if (!empty($keys)) {
                $this->fetch_all($keys, true);
                $ret = true;
            }
        }
        return $ret;
    }

    public function increase_cache($ids, $data, $cache_ttl = null, $pre_cache_key = null)
    {
        if ($this->allowmem) {
            if (($cache_data = $this->fetch_cache($ids, $pre_cache_key)) !== false) {
                foreach ($cache_data as $id => $one) {
                    foreach ($data as $key => $value) {
                        if (is_array($value)) {
                            $one[$key] = $value[0];
                        } else {
                            $one[$key] = $one[$key] + ($value);
                        }
                    }
                    $this->store_cache($id, $one, $cache_ttl, $pre_cache_key);
                }
            }
        }
    }

    public function __toString()
    {
        return $this->name;
    }

    protected function _init_extend()
    {
        
    }

    public function attach_before_method($name, $fn)
    {
        $this->methods[$name][0][] = $fn;
    }

    public function attach_after_method($name, $fn)
    {
        $this->methods[$name][1][] = $fn;
    }

}
