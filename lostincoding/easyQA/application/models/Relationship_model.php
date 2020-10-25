<?php

/**
 * 用户关系处理
 */
class Relationship_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($user_id, $ruser_id)
    {
        $where = array(
            'user_id' => $user_id,
            'ruser_id' => $ruser_id,
        );
        $query = $this->db->select('*')
            ->from('relationship_tb')
            ->where($where)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 关注
     */
    public function follow($user_id, $ruser_id, $group_id = 0)
    {
        $rtype = 1;
        //查询对方是否已关注自己
        $r_relationship = $this->get($ruser_id, $user_id);
        if (is_array($r_relationship)) {
            //对方已单向关注自己，改为互相关注
            if ($r_relationship['rtype'] == 1) {
                $rtype = 2;
                $relationship = array(
                    'user_id' => $ruser_id,
                    'ruser_id' => $user_id,
                    'rtype' => $rtype,
                );
                $this->update($relationship);
            }
        }

        //关注
        $relationship = array(
            'user_id' => $user_id,
            'ruser_id' => $ruser_id,
            'group_id' => $group_id,
            'rtype' => $rtype,
        );
        return $this->add($relationship);
    }

    /**
     * 取消关注
     */
    public function unfollow($user_id, $ruser_id)
    {
        //查询对方是否已关注自己
        $r_relationship = $this->get($ruser_id, $user_id);
        if (is_array($r_relationship)) {
            //对方已互相关注自己，改为单向关注
            if ($r_relationship['rtype'] == 2) {
                $relationship = array(
                    'user_id' => $ruser_id,
                    'ruser_id' => $user_id,
                    'rtype' => 1,
                );
                $this->update($relationship);
            }
        }

        return $this->del($user_id, $ruser_id);
    }

    public function add($relationship)
    {
        if ($this->db->insert('relationship_tb', $relationship)) {
            return $this->get($relationship['user_id'], $relationship['ruser_id']);
        }
        return 0;
    }

    public function update($relationship)
    {
        $where = array(
            'user_id' => $relationship['user_id'],
            'ruser_id' => $relationship['ruser_id'],
        );
        if ($this->db->update('relationship_tb', $relationship, $where)) {
            return $this->get($relationship['user_id'], $relationship['ruser_id']);
        }
        return 0;
    }

    public function del($user_id, $ruser_id)
    {
        $where = array(
            'user_id' => $user_id,
            'ruser_id' => $ruser_id,
        );
        return $this->db->delete('relationship_tb', $where);
    }

}
