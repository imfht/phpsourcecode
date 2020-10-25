<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 16-4-7
 * Time: 下午4:34
 */
class Chapter_model extends CI_Model {

    private $chapter_cache_time = 30000;

    public function __construct() {
        $this->load->database();
        $this->load->model('setting_model', 'setting');
        //读取设置中的缓存时间
        //$this->chapter_cache_time = $this->setting->get('chapter_cache_time');
        //$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }

    /**
     * @param null /string $id
     * @param null /string $story_id
     * @param null /string $num
     * @param null /string $offset
     *
     * @return array
     */
    public function get($id = null, $story_id = null, $num = null, $offset = null, $where = null) {
        if ($where != null) {
            $this->db->where($where);
        }

        if ($id) {
            //$this->db->cache_on();
            $chapter = $this->db->get_where('chapter', array('id' => $id))->row_array();
            //$this->db->cache_off();
            return $chapter;
        }

        if ($story_id) {
            $this->db->select('id,title');
            $this->db->where('story_id', $story_id);
            $this->db->order_by('order', 'asc');
        }

        if ($num || $offset) {
            $this->db->limit($num, $offset);
        }

        return $this->db->get('chapter')->result_array();
    }

    public function insert($chapter) {
        //自动更新ID号
        $sql = 'UPDATE chapter_id as a,chapter_id as b set a.id=(b.id+1)';

        $this->db->query($sql);
        $chapter_id    = $this->db->select('id')->from('chapter_id')->limit(1)->get()->row_array();
        $chapter['id'] = $chapter_id['id'];
        $table_name    = $this->get_table_name($chapter['id']);
        $this->db->replace($table_name, $chapter);
    }

    public function update($chapter) {
        $table_name = $this->get_table_name($chapter['id']);
        $this->db->where('id', $chapter['id']);
        $this->db->update($table_name, $chapter);
    }

    public function get_table_name($id) {
        return 'chapter_' . intval($id) % 10;
    }

    /**
     * 查询文章的上一条及下一条记录ID
     *
     * @param $id
     *
     * @return array
     */
    public function get_pn($id) {
        //if (!$prev_next = $this->cache->get('chapter_prev_next_' . $id)) {//检查缓存
            $this->db->select('id,story_id,order');//获取当前章节
            $c = $this->db->get_where('chapter', array('id' => $id))->row_array();
            //获取下一章节
            $this->db->select('id');
            $this->db->where(array('order >' => $c['order'], 'story_id' => $c['story_id']));
            $this->db->order_by('order', 'ASC');
            $this->db->limit(1);
            $next = $this->db->get('chapter')->row_array();

            $this->db->select('id');
            $this->db->where(array('order <' => $c['order'], 'story_id' => $c['story_id']));
            $this->db->order_by('order', 'DESC');
            $this->db->limit(1);
            $prev = $this->db->get('chapter')->row_array();

            $prev_next = array('next' => $next['id'], 'prev' => $prev['id'], 'story_id' => $c['story_id']);

            //$this->cache->save('chapter_prev_next_' . $id, $prev_next, $this->chapter_cache_time);//存入缓存
        //}

        return $prev_next;
    }

    /**
     *
     * 章节内容过滤
     * @param $content
     *
     * @return string
     */
    public function filter($content) {
        $this->load->model('setting_model', 'setting');
        $filter = $this->setting->get('content_filter');
        $filter = json_decode($filter, true);

        foreach ($filter as $val) {
            $patterns[] = '#' . str_replace('%%', '(.+)', $val['t']) . '#U';
            $replace[]  = $val['c'];
        }
        $patterns[] = '#[\r\n]#U';
        $replace[]  = '';

        $content = preg_replace($patterns, $replace, $content);
        //去除html标签，只保留p\img\br
        return strip_tags($content, '<p><img><br>');
    }

    public function all($story_id = null, $where = null) {
        $this->db->select('id');
        if ($where) {
            $this->db->where($where);
        }
        if ($story_id) {
            $this->db->where('story_id', $story_id);
        }
        return $this->db->count_all_results('chapter');
    }


    /**
     * 比较数据库已存章节和采集到的章节，得出差集 *
     * @chancelai $arr1
     * @chancelai $arr2 *
     * @return array
     *
     */
    function unique($arr1, $arr2) {
        foreach ($arr2 as $key => $c) {
            $arr2[$key]['order'] = $key;
            foreach ($arr1 as $j => $s) {
                if (trim($c['title']) == trim($s['title'])) {
                    unset($arr2[$key]);
                    unset($arr1[$j]);
                    break;
                }
            }

        }
        return $arr2;
    }

}