<?php

/**
 * 文章投票处理
 */
class Articlevote_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加投票
     */
    public function add($vote)
    {
        return $this->db->insert('article_vote_tb', $vote);
    }

    /**
     * 删除1个文章的所有投票
     */
    public function del_by_articleId($article_id)
    {
        $where = array(
            'article_id' => $article_id,
        );
        return $this->db->delete('article_vote_tb', $where);
    }

    /**
     * 根据文章id和用户id获取投票，可判断出用户是否已经投票过
     */
    public function get_by_id_and_userId($article_id, $user_id)
    {
        $where = array(
            'article_id' => $article_id,
            'user_id' => $user_id,
        );
        $query = $this->db->select('*')
            ->from('article_vote_tb')
            ->where($where)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }
}
