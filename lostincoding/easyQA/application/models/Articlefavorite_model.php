<?php

/**
 * 文章收藏处理
 */
class Articlefavorite_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($favorite_id)
    {
        $query = $this->db->select('*')
            ->from('article_favorite_tb')
            ->where('favorite_id', $favorite_id)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    public function get_by_userId_and_articleId($user_id, $article_id)
    {
        $where = array(
            'user_id' => $user_id,
            'article_id' => $article_id,
        );
        $query = $this->db->select('*')
            ->from('article_favorite_tb')
            ->where($where)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    public function gets_by_userId($user_id, $page_index, $page_size)
    {
        $where = array(
            'A.user_id' => $user_id,
        );
        $start = ($page_index - 1) * $page_size;
        $query = $this->db->select('A.id AS favorite_id, B.id, B.article_type, B.article_status, B.article_title, B.is_top, B.is_fine, B.view_counts, B.comment_counts, B.vote_counts, B.vote_up_counts, B.vote_down_counts, B.user_id, B.add_time, C.nickname, C.avatar_ext, C.verify_type, C.verify_details')
            ->from('article_favorite_tb A')
            ->join('article_tb B', 'A.article_id = B.id', 'inner')
            ->join('user_tb C', 'A.user_id = C.id', 'inner')
            ->where($where)
            ->order_by('A.id DESC')
            ->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    public function get_counts_by_userId($user_id)
    {
        $where = array(
            'user_id' => $user_id,
        );
        return $this->db->select('id')
            ->from('article_favorite_tb')
            ->where($where)
            ->count_all_results();
    }

    public function add($favorite)
    {
        return $this->db->insert('article_favorite_tb', $favorite);
    }

    public function del($favorite_id)
    {
        $where = array(
            'id' => $favorite_id,
        );
        return $this->db->delete('article_favorite_tb', $where);
    }
}
