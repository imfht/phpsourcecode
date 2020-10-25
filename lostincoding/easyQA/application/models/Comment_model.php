<?php

/**
 * 评论处理
 */
class Comment_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取评论
     */
    public function get($id, $user_id = null)
    {
        $vote_sql = !empty($user_id) ? ', C.vote_type' : '';
        $this->db->select('A.*, B.nickname, B.avatar_ext, B.avatar_ext, B.verify_type, B.verify_details' . $vote_sql)
            ->from('comment_tb A')
            ->join('user_tb B', 'A.user_id = B.id', 'inner');
        if (!empty($user_id)) {
            $this->db->join('comment_vote_tb C', 'A.id = C.comment_id AND C.user_id = ' . $user_id, 'left');
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
     * 文章最新评论列表
     */
    public function gets_by_articleId($article_id, $page_index, $page_size, $user_id = null)
    {
        $vote_sql = !empty($user_id) ? ', C.vote_type' : '';
        $start = ($page_index - 1) * $page_size;
        $this->db->select('A.*, B.nickname, B.avatar_ext, B.avatar_ext, B.verify_type, B.verify_details' . $vote_sql)
            ->from('comment_tb A')
            ->join('user_tb B', 'A.user_id = B.id', 'inner');
        if (!empty($user_id)) {
            $this->db->join('comment_vote_tb C', 'A.id = C.comment_id AND C.user_id = ' . $user_id, 'left');
        }
        $query = $this->db->where('A.article_id', $article_id)
            ->order_by('A.comment_status DESC, A.id ASC')
            ->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 文章评论总数
     */
    public function get_counts_by_articleId($article_id)
    {
        return $this->db->select('id')
            ->from('comment_tb')
            ->where('article_id', $article_id)
            ->count_all_results();
    }

    /**
     * 最新评论列表
     */
    public function gets_by_latest($page_index, $page_size)
    {
        $start = ($page_index - 1) * $page_size;
        $query = $this->db->select('A.*, B.nickname, B.avatar_ext, B.avatar_ext, B.verify_type, B.verify_details, C.article_type')
            ->from('comment_tb A')
            ->join('user_tb B', 'A.user_id = B.id', 'inner')
            ->join('article_tb C', 'A.article_id = C.id', 'left')
            ->order_by('A.id DESC')
            ->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 评论总数
     */
    public function get_counts()
    {
        return $this->db->select('id')
            ->from('comment_tb')
            ->count_all_results();
    }

    /**
     * 指定用户最新评论列表
     */
    public function gets_by_userId($user_id, $page_index, $page_size)
    {
        $start = ($page_index - 1) * $page_size;
        $query = $this->db->select('A.*, C.id AS article_id, C.article_title, C.is_top, C.is_fine')
            ->from('comment_tb A')
            ->join('article_tb C', 'A.article_id = C.id', 'inner')
            ->where('A.user_id', $user_id)
            ->order_by('A.id DESC')
            ->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 指定用户的评论总数
     */
    public function get_counts_by_userId($user_id)
    {
        return $this->db->select('id')
            ->from('comment_tb')
            ->where('user_id', $user_id)
            ->count_all_results();
    }

    /**
     * 获取热门评论用户
     */
    public function get_top_users($page_index, $page_size)
    {
        $start = ($page_index - 1) * $page_size;
        $sql = 'SELECT A.user_id AS id, COUNT(*) AS comment_counts, B.nickname, B.avatar_ext, B.avatar_ext, B.verify_type, B.verify_details FROM comment_tb A
INNER JOIN user_tb B
ON A.user_id = B.id
GROUP BY A.user_id
HAVING comment_counts > 0
ORDER by comment_counts DESC
LIMIT ?, ?';
        $query = $this->db->query($sql, array($start, $page_size));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 添加评论
     */
    public function add($comment)
    {
        if ($this->db->insert('comment_tb', $comment)) {
            $id = $this->db->insert_id();
            return $this->get($id);
        }
        return 0;
    }

    /**
     * 更新评论
     */
    public function update($comment)
    {
        $where = array(
            'id' => $comment['id'],
        );
        return $this->db->update('comment_tb', $comment, $where);
    }

    /**
     * 删除评论
     */
    public function del($id)
    {
        $where = array(
            'id' => $id,
        );
        return $this->db->delete('comment_tb', $where);
    }

    /**
     * 删除1个文章的所有评论
     */
    public function del_by_articleId($article_id)
    {
        $where = array(
            'article_id' => $article_id,
        );
        return $this->db->delete('comment_tb', $where);
    }

    /**
     * 提问投票
     */
    public function vote($comment_id, $vote_type)
    {
        $where = array(
            'id' => $comment_id,
        );
        if ($vote_type == 1) {
            $this->db->set('vote_counts', 'vote_counts + 1', false);
            $this->db->set('vote_up_counts', 'vote_up_counts + 1', false);
        } else if ($vote_type == 2) {
            $this->db->set('vote_counts', 'vote_counts - 1', false);
            $this->db->set('vote_down_counts', 'vote_down_counts + 1', false);
        }
        $this->db->where('id', $comment_id);
        return $this->db->update('comment_tb');
    }

    /**
     * 获取对话双方用户id
     */
    public function get_dialog_userIds($dialog_id)
    {
        $query = $this->db->select('*')
            ->from('comment_tb')
            ->where('dialog_id', $dialog_id)
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            $dialog = $query->row_array();
            $userIds = array($dialog['user_id']);
            //取被回复的原评论用户id
            $comment = $this->get($dialog['reply_comment_id']);
            if (is_array($comment) && $comment['user_id'] != $userIds[0]) {
                $userIds[] = $comment['user_id'];
            }
            return $userIds;
        }
        return 0;
    }

    /**
     * 获取对话列表
     */
    public function get_dialogs($dialog_id, $user_id = null, $page_index = null, $page_size = null)
    {
        $vote_sql = !empty($user_id) ? ', C.vote_type' : '';
        $this->db->select('A.*, B.nickname, B.avatar_ext, B.avatar_ext, B.verify_type, B.verify_details' . $vote_sql)
            ->from('comment_tb A')
            ->join('user_tb B', 'A.user_id = B.id', 'inner');
        if (!empty($user_id)) {
            $this->db->join('comment_vote_tb C', 'A.id = C.comment_id AND C.user_id = ' . $user_id, 'left');
        }
        $this->db->where('A.dialog_id', $dialog_id)
            ->order_by('A.id ASC');
        if (!empty($page_index)) {
            $start = ($page_index - 1) * $page_size;
            $this->limit($page_size, $start);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $dialogs = $query->result_array();
            //如果是第一页，则需要获取被回复的原评论
            if (empty($page_index) || $page_index == 1) {
                $comment = $this->get($dialogs[0]['reply_comment_id'], $user_id);
                if (is_array($comment)) {
                    $comment['dialog_id'] = $dialog_id;
                    array_unshift($dialogs, $comment);
                }
            }
            return $dialogs;
        }
        return 0;
    }
}
