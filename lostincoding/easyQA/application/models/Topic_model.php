<?php

/**
 * 话题管理处理
 */
class Topic_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取1条话题
     */
    public function get($id)
    {
        $query = $this->db->select('*')
            ->from('topic_tb')
            ->where('id', $id)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 获取话题列表
     * $order_by 0=最热 1=最新
     */
    public function gets($page_index, $page_size, $order_by)
    {
        $start = ($page_index - 1) * $page_size;
        $this->db->select('*')
            ->from('topic_tb');
        if ($order_by == 0) {
            $this->db->order_by('used_times', 'DESC');
        } else if ($order_by == 1) {
            $this->db->order_by('add_time', 'DESC');
        }
        $query = $this->db->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 话题总数
     */
    public function get_counts()
    {
        $this->db->select('id')
            ->from('topic_tb');
        return $this->db->count_all_results();
    }

    /**
     * 获取1条话题
     */
    public function get_by_topic($topic)
    {
        $query = $this->db->select('*')
            ->from('topic_tb')
            ->where('topic', $topic)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 更新话题
     */
    public function update($topic)
    {
        $sql = 'INSERT INTO topic_tb(topic)
VALUES (?)
ON DUPLICATE KEY UPDATE
used_times = used_times + 1';
        $this->db->query(
            $sql,
            array(
                $topic,
            )
        );
        return $this->get_by_topic($topic['topic']);
    }

    /**
     * 删除话题
     */
    public function del($id)
    {
        $where = array(
            'id' => $id,
        );
        return $this->db->delete('topic_tb', $where);
    }
}
