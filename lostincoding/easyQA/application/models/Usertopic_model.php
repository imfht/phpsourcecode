<?php

/**
 * 用户话题处理
 */
class Usertopic_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取1条用户话题
     */
    public function get($id)
    {
        $query = $this->db->select('A.*, B.topic')
            ->from('user_topic_tb A')
            ->join('topic_tb B', 'A.topic_id = B.id', 'inner')
            ->where('A.id', $id)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 获取用户话题列表
     * @param  int    $user_id    用户id
     * @param  int    $order_by   0=最热 1=最新
     * @param  int    $page_index 页码,从1开始
     * @param  int    $page_size  每页数目
     * @return array              用户话题列表,没有结果返回0
     */
    public function gets_by_userId($user_id, $page_index, $page_size, $order_by)
    {
        $start = ($page_index - 1) * $page_size;
        $where = array(
            'A.user_id' => $user_id,
        );
        $this->db->select('A.*, B.topic')
            ->from('user_topic_tb A')
            ->join('topic_tb B', 'A.topic_id = B.id', 'inner')
            ->where($where);
        if ($order_by == 0) {
            $this->db->order_by('used_times', 'DESC');
        } else if ($order_by == 1) {
            $this->db->order_by('last_use_time', 'DESC');
        }
        $query = $this->db->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 更新用户话题
     */
    public function update($user_topic)
    {
        $sql = 'INSERT INTO user_topic_tb(user_id, topic_id)
VALUES (?, ?)
ON DUPLICATE KEY UPDATE
used_times = used_times + 1,
last_use_time = ?';
        return $this->db->query(
            $sql,
            array(
                $user_topic['user_id'],
                $user_topic['topic_id'],
                $this->now,
            )
        );
    }

    /**
     * 删除用户话题
     */
    public function del($id)
    {
        $where = array(
            'id' => $id,
        );
        return $this->db->delete('user_topic_tb', $where);
    }
}
