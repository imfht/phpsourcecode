<?php

/**
 * 皮肤处理
 */
class Skin_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取皮肤
     * @param  int   $id 皮肤id
     * @return array     皮肤
     */
    public function get($id)
    {
        $query = $this->db->select('*')
            ->from('skin_tb')
            ->where('id', $id)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 获取皮肤列表
     * @param  int    $page_index 页码,从1开始
     * @param  int    $page_size  每页数目
     * @param  int    $class      皮肤分类,最新new,最热hot
     * @return array              皮肤列表,没有结果返回0
     */
    public function gets($page_index, $page_size, $class = 'new')
    {
        $start = ($page_index - 1) * $page_size;
        $this->db->select('*')->from('skin_tb');
        if (!in_array($class, array('new', 'hot'))) {
            $this->db->where('skin_class', $class);
        }
        if ($class == 'new') {
            $this->db->order_by('add_time', 'DESC');
        } else {
            $this->db->order_by('skin_stats', 'DESC');
        }
        $query = $this->db->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 获取皮肤数目
     * @param  int $class 皮肤分类,最新new,最热hot
     * @return int        皮肤数目
     */
    public function gets_count($class)
    {
        $this->db->select('id')->from('skin_tb');
        if (!in_array($class, array('new', 'hot'))) {
            $this->db->where('skin_class', $class);
        }
        return $this->db->count_all_results();
    }

    /**
     * 皮肤使用次数增加1
     * @param  int $id 皮肤id
     * @return int     皮肤使用次数
     */
    public function skin_stats_plus($id)
    {
        $this->db->set('skin_stats', 'skin_stats + 1', false);
        $this->db->where('id', $id);
        if ($this->db->update('skin_tb')) {
            $skin = $this->get($id);
            return $skin['skin_stats'];
        }
        return 0;
    }

}
