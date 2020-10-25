<?php

class tree {

    /**
     * @var Driver_Db_Dao
     */
    protected $db;
    // pid的字段名
    protected $pidkey;

    protected $icon = array('　│ ', '　├─', '　└─', '　　');

    public function __construct($model, $pidkey = 'pid') {
        $this->db = $model;
        $this->pidkey = $pidkey;
    }

    public function getList($pid = 0, $field = '*', $where = array(), $order = 'ordernum asc,id asc', $level = 1) {
        $where[$this->pidkey] = $pid;
        $data = $this->db->field($field)->where($where)->order($order)->select();
        if ($data === null) return array();
        $list = array();
        foreach ($data as $v) {
            $v['level'] = $level;
            $list[] = $v;
            $sons = $this->getlist($v[$this->db->getPk()], $field, $where, $order, $level + 1);
            $list = array_merge($list, $sons);
        }
        return $list;
    }

    public function getIconList($list, $startlevel = 1) {
        if ($startlevel > 1) {
            $preicon[] = array_fill(0, $startlevel - 1, $this->icon['3']);
        } else {
            $preicon = array();
        }

        foreach ($list as $k => &$v) {
            $v['showname'] = $v['name'];
            if ($v['level'] >= $startlevel) {
                $icon = $this->icon['2'];
                $preicon[$v['level'] - $startlevel] = $this->icon['3'];
                foreach (array_slice($list, $k + 1) as $n) {
                    if ($n['level'] < $v['level']) {
                        //后面没有同级的
                        $preicon[$v['level'] - $startlevel] = $this->icon['3'];
                        break;
                    } elseif ($n['level'] == $v['level']) {
                        //后面没有同级的
                        $icon = $this->icon['1'];
                        $preicon[$v['level'] - $startlevel] = $this->icon['0'];
                        break;
                    }
                }
                $v['showname'] = implode('', array_slice($preicon, 0, $v['level'] - $startlevel)) . $icon . $v['name'];
            }
        }
        return $list;
    }

    public function getSonList($pid = 0, $field = '*', $where = array(), $limitlevel = 3, $order = 'ordernum asc,id asc', $level = 1) {
        $where[$this->pidkey] = $pid;
        $data = $this->db->field($field)->where($where)->order($order)->select();
        if ($data === null) return array();
        $list = array();
        foreach ($data as $v) {
            $v['level'] = $level;
            if (isset($v['module'])) {
                $v['url'] = ($v['module'] == '') ? '' : U($v['module'] . '.' . $v['controller'] . '.' . $v['action']);
                unset($v['module'], $v['controller'], $v['action']);
            }
            if ($level < $limitlevel) {
                $v['sons'] = $this->getSonList($v[$this->db->getPk()], $field, $where, $limitlevel, $order, $level + 1);
            }
            $list[] = $v;
        }
        return $list;
    }

    public function getAuthList($pid = 0, $field = '*', $where = array(), $order = 'ordernum asc,id asc', $level = 1) {
        $where[$this->pidkey] = $pid;
        $data = $this->db->field($field)->where($where)->order($order)->select();
        if ($data === null) return array();
        $list = array();
        foreach ($data as $v) {
            $v['level'] = $level;
            if ($level < 4) {
                $son=$this->getAuthList($v[$this->db->getPk()], $field, $where, $order, $level + 1);
                if ($level == 3) {
                    $list = array_merge($list, array($v), $son);
                } else {
                    $v['sons'] = $son;
                    $list[] = $v;
                }
            }else{
                $list[] = $v;
            }
        }
        return $list;
    }
}