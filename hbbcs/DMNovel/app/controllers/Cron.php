<?php

/**
 *
 *计划任务：自动更新小说
 * author: ChinaLiaoTian
 */
class Cron extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('category_model', 'category');
        $this->load->model('collect_model', 'collect');
        $this->load->model('story_model', 'story');
        $this->load->model('chapter_model', 'chapter');
        $this->load->model('ccache_model', 'ccache');
    }

    public function index() {
        ignore_user_abort(true);
        set_time_limit(0);

        $this->output->enable_profiler(TRUE);

        //采集小说列表
        $collect_lists = $this->ccache->get();

        //开始采集
        foreach ($collect_lists as $story) {
            //获取已采集章节列表
            $cache = $this->chapter->get(null,$story['story_id']);
            //获取现在的章节列表
            $chapter_list = $this->collect->getChapterList($story['list_url'], $story['collect_id']);
            //比较是否有更新
            $arr   = $this->chapter->unique($cache, $chapter_list);
            //有更新，采集新章节
            if ($arr) {
                foreach ($arr as $v) {
                    $chapter_url = $story['chapter_url'] . '/' . $v['url'];
                    $chapter     = [
                        'content'  => $this->collect->getChapter($chapter_url, $story['collect_id']),
                        'order'    => $v['order'] ? $v['order'] : 0,
                        'story_id' => $story['story_id'],
                        'title'    => $v['title']
                    ];
                    if ($chapter['content']) {
                        $this->chapter->insert($chapter);
                        $this->db->set('last_update', date('Y-m-d H:i:s'))->where('id', $chapter['story_id'])->update('story');
                    }
                }
                //写入日志
                log_message('error','collect story '.$story['title'].' ok');
            }
        }
    }

}
