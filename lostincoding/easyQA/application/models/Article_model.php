<?php

/**
 * 文章处理
 */
class Article_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取文章
     */
    public function get($id, $get_q_ontent = false, $user_id = null)
    {
        $q_content_sql = $get_q_ontent ? ' A.article_content,' : '';
        $vote_sql = !empty($user_id) ? ', C.vote_type' : '';
        $favorite_sql = !empty($user_id) ? ', D.id AS favorite_id' : '';
        $this->db->select('A.id, A.article_type, A.article_status, A.article_title,' . $q_content_sql . ' A.is_top, A.is_fine, A.view_counts, A.comment_counts, A.vote_counts, A.vote_up_counts, A.vote_down_counts, A.user_id, A.add_time, B.nickname, B.avatar_ext, B.verify_type, B.verify_details' . $vote_sql . $favorite_sql)
            ->from('article_tb A')
            ->join('user_tb B', 'A.user_id = B.id', 'inner');
        if (!empty($user_id)) {
            $this->db->join('article_vote_tb C', 'A.id = C.article_id AND C.user_id = ' . $user_id, 'left');
            $this->db->join('article_favorite_tb D', 'A.id = D.article_id AND D.user_id = ' . $user_id, 'left');
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
     * 最新文章列表
     */
    public function gets_by_latest($article_type, $page_index, $page_size, $get_q_ontent = false, $user_id = null)
    {
        $q_content_sql = $get_q_ontent ? ' A.article_content,' : '';
        $vote_sql = !empty($user_id) ? ', C.vote_type' : '';
        $start = ($page_index - 1) * $page_size;
        $this->db->select('A.id, A.article_type, A.article_status, A.article_title,' . $q_content_sql . ' A.is_top, A.is_fine, A.view_counts, A.comment_counts, A.vote_counts, A.vote_up_counts, A.vote_down_counts, A.user_id, A.add_time, B.nickname, B.avatar_ext, B.verify_type, B.verify_details' . $vote_sql)
            ->from('article_tb A')
            ->join('user_tb B', 'A.user_id = B.id', 'inner');
        if (!empty($user_id)) {
            $this->db->join('article_vote_tb C', 'A.id = C.article_id AND C.user_id = ' . $user_id, 'left');
        }
        if (!empty($article_type)) {
            $this->db->where('A.article_type', $article_type);
        }
        $query = $this->db->order_by('A.is_top DESC, A.id DESC')
            ->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 文章总数
     */
    public function get_counts($article_type)
    {
        $this->db->select('id')
            ->from('article_tb');
        if (!empty($article_type)) {
            $this->db->where('article_type', $article_type);
        }
        return $this->db->count_all_results();
    }

    /**
     * 精帖文章列表
     */
    public function gets_by_fine($article_type, $page_index, $page_size, $get_q_ontent = false, $user_id = null)
    {
        $where = array(
            'A.is_fine' => 2,
        );
        if (!empty($article_type)) {
            $where['A.article_type'] = $article_type;
        }
        $q_content_sql = $get_q_ontent ? ' A.article_content,' : '';
        $vote_sql = !empty($user_id) ? ', C.vote_type' : '';
        $start = ($page_index - 1) * $page_size;
        $this->db->select('A.id, A.article_type, A.article_status, A.article_title,' . $q_content_sql . ' A.is_top, A.is_fine, A.view_counts, A.comment_counts, A.vote_counts, A.vote_up_counts, A.vote_down_counts, A.user_id, A.add_time, B.nickname, B.avatar_ext, B.verify_type, B.verify_details' . $vote_sql)
            ->from('article_tb A')
            ->join('user_tb B', 'A.user_id = B.id', 'inner');
        if (!empty($user_id)) {
            $this->db->join('article_vote_tb C', 'A.id = C.article_id AND C.user_id = ' . $user_id, 'left');
        }
        $query = $this->db->where($where)
            ->order_by('A.is_top DESC, A.id DESC')
            ->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 精帖文章总数
     */
    public function get_counts_by_fine($article_type)
    {
        $where = array(
            'is_fine' => 2,
        );
        if (!empty($article_type)) {
            $where['article_type'] = $article_type;
        }
        return $this->db->select('id')
            ->from('article_tb')
            ->where($where)
            ->count_all_results();
    }

    /**
     * 热门文章列表
     * @param  int $hot_type   热门类型,0=浏览数 1=评论数
     * @param  int $days       热门天数
     */
    public function gets_by_hot($article_type, $hot_type, $days = 7, $page_index, $page_size, $get_q_ontent = false, $user_id = null)
    {
        $q_content_sql = $get_q_ontent ? ' A.article_content,' : '';
        $vote_sql = !empty($user_id) ? ', C.vote_type' : '';
        $where = array(
            'A.add_time >' => date('Y-m-d H:i:s', strtotime("-{$days} day")),
        );
        if (!empty($article_type)) {
            $where['A.article_type'] = $article_type;
        }
        $start = ($page_index - 1) * $page_size;
        $this->db->select('A.id, A.article_type, A.article_status, A.article_title,' . $q_content_sql . ' A.is_top, A.is_fine, A.view_counts, A.comment_counts, A.vote_counts, A.vote_up_counts, A.vote_down_counts, A.user_id, A.add_time, B.nickname, B.avatar_ext, B.verify_type, B.verify_details' . $vote_sql)
            ->from('article_tb A')
            ->join('user_tb B', 'A.user_id = B.id', 'inner');
        if (!empty($user_id)) {
            $this->db->join('article_vote_tb C', 'A.id = C.article_id AND C.user_id = ' . $user_id, 'left');
        }
        if ($hot_type == 0) {
            $this->db->order_by('A.view_counts', 'DESC');
        } else if ($hot_type == 1) {
            $this->db->order_by('A.comment_counts', 'DESC');
        }
        $query = $this->db->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 指定用户的文章列表
     */
    public function gets_by_userId($article_type, $user_id, $page_index, $page_size, $get_q_ontent = false)
    {
        $where = array(
            'A.user_id' => $user_id,
        );
        if (!empty($article_type)) {
            $where['A.article_type'] = $article_type;
        }
        $q_content_sql = $get_q_ontent ? ' A.article_content,' : '';
        $start = ($page_index - 1) * $page_size;
        $query = $this->db->select('A.id, A.article_type, A.article_status, A.article_title,' . $q_content_sql . ' A.is_top, A.is_fine, A.view_counts, A.comment_counts, A.vote_counts, A.vote_up_counts, A.vote_down_counts, A.user_id, A.add_time, B.nickname, B.avatar_ext, B.verify_type, B.verify_details')
            ->from('article_tb A')
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
     * 指定用户的文章总数
     */
    public function get_counts_by_userId($article_type, $user_id)
    {
        $where = array(
            'user_id' => $user_id,
        );
        if (!empty($article_type)) {
            $where['article_type'] = $article_type;
        }
        return $this->db->select('id')
            ->from('article_tb')
            ->where($where)
            ->count_all_results();
    }

    /**
     * 获取热门文章用户
     */
    public function get_top_users($page_index, $page_size)
    {
        $start = ($page_index - 1) * $page_size;
        $sql = 'SELECT A.user_id AS id, COUNT(*) AS q_counts, B.nickname, B.avatar_ext, B.verify_type, B.verify_details FROM article_tb A
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
     * 增加文章阅读数
     */
    public function add_view_counts($id)
    {
        $this->db->set('view_counts', 'view_counts + 1', false);
        $this->db->where('id', $id);
        return $this->db->update('article_tb');
    }

    /**
     * 增加文章评论数
     */
    public function add_comment_counts($id)
    {
        $this->db->set('comment_counts', 'comment_counts + 1', false);
        $this->db->where('id', $id);
        return $this->db->update('article_tb');
    }

    /**
     * 减少文章评论数
     */
    public function reduce_comment_counts($id)
    {
        $this->db->set('comment_counts', 'comment_counts - 1', false);
        $this->db->where('id', $id);
        return $this->db->update('article_tb');
    }

    /**
     * 添加文章
     */
    public function add($article)
    {
        if ($this->db->insert('article_tb', $article)) {
            $id = $this->db->insert_id();
            return $this->get($id);
        }
        return 0;
    }

    /**
     * 更新文章
     */
    public function update($article)
    {
        $where = array(
            'id' => $article['id'],
        );
        return $this->db->update('article_tb', $article, $where);
    }

    /**
     * 删除文章
     */
    public function del($id)
    {
        $where = array(
            'id' => $id,
        );
        return $this->db->delete('article_tb', $where);
    }

    /**
     * 文章投票
     */
    public function vote($article_id, $vote_type)
    {
        $where = array(
            'id' => $article_id,
        );
        if ($vote_type == 1) {
            $this->db->set('vote_counts', 'vote_counts + 1', false);
            $this->db->set('vote_up_counts', 'vote_up_counts + 1', false);
        } else if ($vote_type == 2) {
            $this->db->set('vote_counts', 'vote_counts - 1', false);
            $this->db->set('vote_down_counts', 'vote_down_counts + 1', false);
        }
        $this->db->where('id', $article_id);
        return $this->db->update('article_tb');
    }
}
