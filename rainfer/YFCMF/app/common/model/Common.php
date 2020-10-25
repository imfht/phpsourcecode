<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Model;

/**
 * 公共模型
 * @Author: rainfer <rainfer520@qq.com>
 */
class Common extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '';
    protected $pk    = '';

    /**
     * 设置模型表名
     *
     * @param string $table
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * 设置模型表主键
     *
     * @param string $pk
     *
     * @return $this
     */
    public function setPk($pk = 'id')
    {
        $this->pk = $pk;
        return $this;
    }

    /**
     * 获取子孙级ids
     *
     * @param array  $lists
     * @param bool   $only_id
     * @param int    $pid
     * @param bool   $self
     * @param string $pid_name
     * @param string $id_name
     * @param array  $where
     *
     * @return array
     */
    public function getAllChilds($lists = [], $pid = 0, $only_id = true, $self = false, $pid_name = 'pid', $id_name = 'id', $where = [])
    {
        $result = cache($this->table . '_allchilds_' . $pid . '_' . $only_id . '_' . $self);
        if (!$result) {
            //根据条件取数据
            if (is_array($lists) && !$lists) {
                $lists = $this->where($where)->column('*', $id_name);
            }
            if (is_array($lists) && $lists) {
                foreach ($lists as $id => $a) {
                    if ($a[$pid_name] == $pid) {
                        $result[] = $only_id ? $a[$id_name] : $a;
                        unset($lists[$id]);
                        $result = array_merge($result, $this->getAllChilds($lists, $a[$id_name], $only_id, $self));
                    } elseif ($self && $a[$id_name] == $pid) {
                        $result[] = $only_id ? $a[$id_name] : $a;
                    }
                }
            }
            cache($this->table . '_allchilds_' . $pid . '_' . $only_id . '_' . $self, $result);
        }
        return $result;
    }
}
