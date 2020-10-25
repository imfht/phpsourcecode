<?php

/**
 * 冒泡评论投票处理
 */
class Maopaocommentvote_model extends MY_Model
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
        return $this->db->insert('maopao_comment_vote_tb', $vote);
    }

    /**
     * 根据冒泡评论id删除投票
     */
    public function del_by_commentId($comment_id)
    {
        $where = array(
            'comment_id' => $comment_id,
        );
        return $this->db->delete('maopao_comment_vote_tb', $where);
    }

    /**
     * 删除1个冒泡的所有评论投票
     */
    public function del_by_maopaoId($maopao_id)
    {
        $where = array(
            'maopao_id' => $maopao_id,
        );
        return $this->db->delete('maopao_comment_vote_tb', $where);
    }

    /**
     * 根据冒泡评论id和用户id获取投票，可判断出用户是否已经投票过
     */
    public function getByCommentIdAndUserId($comment_id, $user_id)
    {
        $where = array(
            'comment_id' => $comment_id,
            'user_id' => $user_id,
        );
        $query = $this->db->select('*')
            ->from('maopao_comment_vote_tb')
            ->where($where)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }
}
