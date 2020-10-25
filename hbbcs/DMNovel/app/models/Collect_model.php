<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 16-7-7
 * Time: 下午5:19
 */
class Collect_model extends CI_Model {

    public $id = 0;
    private $site;
    private $book_list;

    public function __construct() {
        $this->load->database();
        //$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        $this->load->library('query');
    }

    /**
     * @param null /num $id
     * @param null /string $select
     *
     * @return array
     */
    public function get($id = null, $select = null) {
        if ($select) {
            $this->db->select($select);
        }
        if ($id) {
            return $this->db->get_where('collect', array('id' => $id))->row_array();
        }
        return $this->db->get('collect')->result_array();
    }

    public function getBookInfo($id, $book_id = null) {
        $this->id   = $id;
        $this->site = $this->get($id);

        $book_id = $book_id ? $book_id : $this->site['test_id']; //如果没有ID，使用测试ID

        //是否截取ID
        if (preg_match('/\[(\d+)\]/', $this->site['book_url'], $match)) {
            $this->site['book_url'] = preg_replace('/(:book_id\[(\d+)\])/', substr($book_id, 0, (int) $match[1]), $this->site['book_url']);
        }
        $this->site['book_url'] = preg_replace('/(:book_id)/', $book_id, $this->site['book_url']);

        $this->query->site($this->site);
        $book_info = $this->query->bookInfo($book_id);

        $this->book_list = $book_info['book_list'];

        return $book_info;
    }

    public function getChapterList($url=null,$collect_id=0) {
        if ($collect_id) {
            $site = $this->get($collect_id);
            $this->query->site($site);
        }
        $url = $url ? $url : $this->book_list;

        return $this->query->chapterList($url);
    }

    public function getChapter($url,$collect_id=0) {
        if ($collect_id) {
            $site = $this->get($collect_id);
            $this->query->site($site);
        }
        return $this->query->chapter($url);
    }

    /**
     * 比较数据库已存章节和采集到的章节，得出差集
     *
     * @param array $sql
     * @param array $capture
     *
     * @return array
     */
    function checkChapterList($sql, $capture) {
        foreach ($capture as $key => $c) {
            $capture[$key]['order'] = $key;
            $j                      = 0;
            foreach ($sql as $s) {
                if ($c['title'] == $s['title']) {
                    unset($capture[$key]);
                    unset($sql[$j]);
                    break;
                }
                $j++;
            }
        }
        return $capture;
    }

}