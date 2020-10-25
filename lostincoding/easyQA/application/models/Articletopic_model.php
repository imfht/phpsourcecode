<?php

/**
 * 话题处理
 */
class Articletopic_model extends MY_Model
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
        $query = $this->db->select('A.*, B.topic')
            ->from('article_topic_tb A')
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
     * 获取文章的话题列表
     */
    public function gets_by_articleId($article_id)
    {
        $query = $this->db->select('A.*, B.topic')
            ->from('article_topic_tb A')
            ->join('topic_tb B', 'A.topic_id = B.id', 'inner')
            ->where('A.article_id', $article_id)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 根据话题id获取文章列表
     * @param  int    $order_by   0=浏览最多 1=评论最多 2=最新
     */
    public function gets_by_topicId($topic_id, $article_type, $page_index, $page_size, $order_by, $get_q_ontent = false, $user_id = null)
    {
        $q_content_sql = $get_q_ontent ? ' B.article_content,' : '';
        $vote_sql = !empty($user_id) ? ', D.vote_type' : '';
        $start = ($page_index - 1) * $page_size;
        $this->db->select('A.*, B.id, B.article_type, B.article_status, B.article_title,' . $q_content_sql . ' B.is_top, B.is_fine, B.view_counts, B.comment_counts, B.vote_counts, B.vote_up_counts, B.vote_down_counts, B.user_id, B.add_time, C.nickname, C.avatar_ext, C.verify_type, C.verify_details' . $vote_sql)
            ->from('article_topic_tb A')
            ->join('article_tb B', 'A.article_id = B.id', 'inner')
            ->join('user_tb C', 'B.user_id = C.id', 'inner');
        if (!empty($user_id)) {
            $this->db->join('article_vote_tb D', 'B.id = D.article_id AND D.user_id = ' . $user_id, 'left');
        }
        $this->db->where('A.topic_id', $topic_id);
        if (!empty($article_type) && $article_type != 'all') {
            $this->db->where('B.article_type', $article_type);
        }
        if ($order_by == 0) {
            $this->db->order_by('B.view_counts', 'DESC');
        } else if ($order_by == 1) {
            $this->db->order_by('B.comment_counts', 'DESC');
        } else if ($order_by == 2) {
            $this->db->order_by('B.id', 'DESC');
        }
        $query = $this->db->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 指定话题文章总数
     */
    public function get_counts_by_topicId($topic_id, $article_type = null)
    {
        $this->db->select('id')
            ->from('article_topic_tb A')
            ->join('article_tb B', 'A.article_id = B.id', 'inner')
            ->where('A.topic_id', $topic_id);
        if (!empty($article_type) && $article_type != 'all') {
            $this->db->where('B.article_type', $article_type);
        }
        return $this->db->count_all_results();
    }

    /**
     * 添加话题
     */
    public function add($article_topic)
    {
        $sql = 'INSERT INTO article_topic_tb(article_id, topic_id)
VALUES (?, ?)
ON DUPLICATE KEY UPDATE
topic_id = ?';
        return $this->db->query(
            $sql,
            array(
                $article_topic['article_id'],
                $article_topic['topic_id'],
                $article_topic['topic_id'],
            )
        );
    }

    /**
     * 删除话题
     */
    public function del($id)
    {
        $where = array(
            'id' => $id,
        );
        return $this->db->delete('article_topic_tb', $where);
    }

    /**
     * 删除1个文章的所有话题
     */
    public function del_by_articleId($article_id)
    {
        $where = array(
            'article_id' => $article_id,
        );
        return $this->db->delete('article_topic_tb', $where);
    }
}
