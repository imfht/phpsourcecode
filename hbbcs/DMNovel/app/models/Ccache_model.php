<?php

/**
 * 采集缓存
 * User: joe
 * Date: 16-8-24
 * Time: 上午11:05
 */
class Ccache_model extends CI_Model {


    public function __construct() {
        $this->load->database();
        $this->load->model('setting_model', 'setting');
        $this->load->model('story_model', 'story');
        $this->load->model('collect_model', 'collect');
    }

    /**
     * 获取采集缓存数据
     *
     * @param $id
     * @param string $select
     * @param string|array $where
     *
     * @return mixed
     */
    public function get($id = null, $select = null, $where = null) {
        if ($select) $this->db->select($select);
        $this->db->order_by('update_time', 'desc');
        if ($id) {
            return $this->db->where('id', $id)->get('collect_cache')->row_array();
        }

        if ($where) {
            $this->db->where($where);
        }

        return $this->db->get('collect_cache')->result_array();
    }

    /**
     * 检查缓存是否存在
     *
     * @param $where
     *
     * @return bool|mixed
     */
    public function check($where) {
        $cache  = [];
        $caches = $this->get(null, null, $where);
        if (!$caches) {
            $cache = $this->get_book($where);
        } else {
            $cache = $caches[0];
        }
        return $cache;
    }

    public function get_book($where) {

        if (!is_array($where) || !$where['collect_id'] || !$where['book_id']) show_error('采集小说条件错误！！！' . var_dump($where));

        $book = $this->collect->getBookInfo($where['collect_id'], $where['book_id']);
        //$chapter_list = $this->collect->getChapterList();

        $book_image = grab_image($book['book_img'], md5($book['book_title']), 'books/' . date('Y', time()) . '/');

        $story = $this->db->select('id')->where('title', $book['book_title'])->get('story')->row_array();
        if (!isset($story)) {
            $book_data = [
                'title'    => $book['book_title'],
                'author'   => $book['book_author'],
                'desc'     => $book['book_desc'],
                'category' => !$where['category_id'] ? 1 : $where['category_id'],
                'time'     => date('Y-m-d H:i'),
                'image'    => $book_image
            ];

            $this->db->insert('story', $book_data);
            $story['id'] = $this->db->insert_id();
        }
        //写入缓存
        $cache = [
            'title'       => $book['book_title'],
            'collect_id'  => $where['collect_id'],
            'book_id'     => $where['book_id'],
            'list_url'    => $book['book_list'],
            'chapter_url' => $book['chapter_url'],
            'story_id'    => $story['id'],
            'category_id' => !$where['category_id'] ? 1 : $where['category_id'],
            'update_time' => date('Y-m-d')
        ];

        $this->db->insert('collect_cache', $cache);

        $cache['id'] = $this->db->insert_id();

        return $cache;

    }

    /**
     * 更新采集缓存
     *
     * @param $where
     * @param $data
     */
    public function update($where, $data) {
        $this->db->where($where)->update('collect_cache', $data);
    }

}