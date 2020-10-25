<?php

/**
 * 签到处理
 */
class Sign_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取签到信息
     */
    public function get($user_id)
    {
        $query = $this->db->select('*')
            ->from('sign_tb')
            ->where('user_id', $user_id)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 签到
     */
    public function sign($user_id, $con_sign_times)
    {
        $sql = 'INSERT INTO sign_tb(user_id)
VALUES (?)
ON DUPLICATE KEY UPDATE
con_sign_times = ?, total_sign_times = total_sign_times + 1, sign_time = ?';
        $this->db->query(
            $sql,
            array(
                $user_id,
                $con_sign_times,
                $this->now,
            )
        );
        return $this->get($user_id);
    }
}
