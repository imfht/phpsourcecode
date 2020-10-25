<?php

/**
 * 皮肤设置处理
 */
class Skinsetting_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取用户设置的皮肤
     * @param  int   $user_id 用户id
     * @return array          用户设置的皮肤信息
     */
    public function get_by_userId($user_id)
    {
        $query = $this->db->select('A.user_id, A.skin_id, A.lock_background, A.setting_time, B.*')
            ->from('skin_setting_tb A')
            ->join('skin_tb B', 'A.skin_id = B.id', 'inner')
            ->where('user_id', $user_id)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 设置皮肤
     * @param int $user_id 用户id
     * @param int $skin_id 皮肤id
     * @return bool             成功返回true,失败返回false
     */
    public function set_user_skin($user_id, $skin_id)
    {
        $sql = 'INSERT INTO skin_setting_tb(user_id, skin_id)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE skin_id = ?';
        return $this->db->query(
            $sql,
            array(
                $user_id,
                $skin_id,
                $skin_id,
            )
        );
    }

    /**
     * 锁定皮肤背景(背景图不随页面滚动)
     * @param  int  $lock_status 1滚动,2不滚动
     * @return bool              成功返回true,失败返回false
     */
    public function lock_background($user_id, $lock_status)
    {
        $sql = 'INSERT INTO skin_setting_tb(user_id, skin_id, lock_background)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE lock_background = ?';
        return $this->db->query(
            $sql,
            array(
                $user_id,
                0,
                $lock_status,
                $lock_status,
            )
        );
    }

}
