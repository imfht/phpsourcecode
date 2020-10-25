<?php

/**
 * 文章追加处理
 */
class Articleappend_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取1条文章追加内容
     */
    public function get($id)
    {
        $query = $this->db->select('*')
            ->from('article_append_tb')
            ->where('id', $id)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 根据文章id获取追加内容列表
     */
    public function gets_by_articleId($article_id)
    {
        $query = $this->db->select('*')
            ->from('article_append_tb')
            ->where('article_id', $article_id)
            ->order_by('id', 'ASC')
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 添加文章追加内容
     */
    public function add($q)
    {
        if ($this->db->insert('article_append_tb', $q)) {
            $id = $this->db->insert_id();
            return $this->get($id);
        }
        return 0;
    }

    /**
     * 删除1个文章的所有追加内容
     */
    public function del_by_articleId($article_id)
    {
        $where = array(
            'article_id' => $article_id,
        );
        return $this->db->delete('article_append_tb', $where);
    }
}
