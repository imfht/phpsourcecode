<?php

/**
 * 冒泡处理
 */
class Maopao_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取冒泡
     */
    public function get($id, $user_id = null)
    {
        $vote_sql = !empty($user_id) ? ', C.vote_type' : '';
        $this->db->select('A.*, B.nickname, B.avatar_ext, B.verify_type, B.verify_details' . $vote_sql)
            ->from('maopao_tb A')
            ->join('user_tb B', 'A.user_id = B.id', 'inner');
        if (!empty($user_id)) {
            $this->db->join('maopao_vote_tb C', 'A.id = C.maopao_id AND C.user_id = ' . $user_id, 'left');
        }
        $query = $this->db->where('A.id', $id)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 最新冒泡列表
     */
    public function gets_by_latest($page_index, $page_size, $user_id = null)
    {
        $vote_sql = !empty($user_id) ? ', C.vote_type' : '';
        $start = ($page_index - 1) * $page_size;
        $this->db->select('A.*, B.nickname, B.avatar_ext, B.verify_type, B.verify_details' . $vote_sql)
            ->from('maopao_tb A')
            ->join('user_tb B', 'A.user_id = B.id', 'inner');
        if (!empty($user_id)) {
            $this->db->join('maopao_vote_tb C', 'A.id = C.maopao_id AND C.user_id = ' . $user_id, 'left');
        }
        $query = $this->db->order_by('A.id DESC')
            ->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 冒泡总数
     */
    public function get_counts()
    {
        $this->db->select('id')
            ->from('maopao_tb');
        return $this->db->count_all_results();
    }

    /**
     * 热门冒泡列表
     * @param  int $hot_type   热门类型,0=浏览数 1=评论数
     * @param  int $days       热门天数
     */
    public function gets_by_hot($hot_type, $days = 7, $page_index, $page_size, $user_id = null)
    {
        $vote_sql = !empty($user_id) ? ', C.vote_type' : '';
        $where = array(
            'A.add_time >' => date('Y-m-d H:i:s', strtotime("-{$days} day")),
        );
        $start = ($page_index - 1) * $page_size;
        $this->db->select('A.*, B.nickname, B.avatar_ext, B.verify_type, B.verify_details' . $vote_sql)
            ->from('maopao_tb A')
            ->join('user_tb B', 'A.user_id = B.id', 'inner');
        if ($hot_type == 0) {
            $this->db->order_by('A.view_counts', 'DESC');
        } else if ($hot_type == 1) {
            $this->db->order_by('A.comment_counts', 'DESC');
        }
        $this->db->order_by('A.id', 'DESC');
        if (!empty($user_id)) {
            $this->db->join('maopao_vote_tb C', 'A.id = C.maopao_id AND C.user_id = ' . $user_id, 'left');
        }
        $query = $this->db->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 指定用户的冒泡列表
     */
    public function gets_by_userId($user_id, $page_index, $page_size)
    {
        $where = array(
            'A.user_id' => $user_id,
        );
        $start = ($page_index - 1) * $page_size;
        $query = $this->db->select('A.*, B.nickname, B.avatar_ext, B.verify_type, B.verify_details')
            ->from('maopao_tb A')
            ->join('user_tb B', 'A.user_id = B.id', 'inner')
            ->where($where)
            ->order_by('A.id DESC')
            ->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 指定用户的冒泡总数
     */
    public function get_counts_by_userId($user_id)
    {
        $where = array(
            'user_id' => $user_id,
        );
        return $this->db->select('id')
            ->from('maopao_tb')
            ->where($where)
            ->count_all_results();
    }

    /**
     * 获取热门冒泡用户
     */
    public function get_top_users($page_index, $page_size)
    {
        $start = ($page_index - 1) * $page_size;
        $sql = 'SELECT A.user_id AS id, COUNT(*) AS q_counts, B.nickname, B.avatar_ext, B.verify_type, B.verify_details FROM maopao_tb A
INNER JOIN user_tb B
ON A.user_id = B.id
GROUP BY A.user_id
HAVING q_counts > 0
ORDER by q_counts DESC
LIMIT ?, ?';
        $query = $this->db->query($sql, array($start, $page_size));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 增加冒泡阅读数
     */
    public function add_view_counts($id)
    {
        $this->db->set('view_counts', 'view_counts + 1', false);
        $this->db->where('id', $id);
        return $this->db->update('maopao_tb');
    }

    /**
     * 增加冒泡评论数
     */
    public function add_comment_counts($id)
    {
        $this->db->set('comment_counts', 'comment_counts + 1', false);
        $this->db->where('id', $id);
        return $this->db->update('maopao_tb');
    }

    /**
     * 减少冒泡评论数
     */
    public function reduce_comment_counts($id)
    {
        $this->db->set('comment_counts', 'comment_counts - 1', false);
        $this->db->where('id', $id);
        return $this->db->update('maopao_tb');
    }

    /**
     * 添加冒泡
     */
    public function add($maopao)
    {
        if ($this->db->insert('maopao_tb', $maopao)) {
            $id = $this->db->insert_id();
            return $this->get($id);
        }
        return 0;
    }

    /**
     * 更新冒泡
     */
    public function update($maopao)
    {
        $where = array(
            'id' => $maopao['id'],
        );
        return $this->db->update('maopao_tb', $maopao, $where);
    }

    /**
     * 删除冒泡
     */
    public function del($id)
    {
        $where = array(
            'id' => $id,
        );
        return $this->db->delete('maopao_tb', $where);
    }

    /**
     * 冒泡投票
     */
    public function vote($maopao_id, $vote_type)
    {
        $where = array(
            'id' => $maopao_id,
        );
        if ($vote_type == 1) {
            $this->db->set('vote_counts', 'vote_counts + 1', false);
            $this->db->set('vote_up_counts', 'vote_up_counts + 1', false);
        } else if ($vote_type == 2) {
            $this->db->set('vote_counts', 'vote_counts - 1', false);
            $this->db->set('vote_down_counts', 'vote_down_counts + 1', false);
        }
        $this->db->where('id', $maopao_id);
        return $this->db->update('maopao_tb');
    }
}
