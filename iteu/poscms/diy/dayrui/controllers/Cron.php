<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */

class Cron extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->output->enable_profiler(FALSE);
    }

    /**
     * 执行任务和队列
     */
    public function index() {

        // 定时处理首页和单页
        $this->load->helper('directory');
        if (SYS_CACHE_INDEX || SYS_CACHE_MINDEX) {
            $files = directory_map(WEBPATH.'cache/index/', 1);
            if ($files) {
                foreach ($files as $t) {
                    $file = WEBPATH.'cache/index/'.$t;
                    if (strpos($t, '-home.html')) {
                        // 首页
                        if (filemtime($file) + SYS_CACHE_INDEX < SYS_TIME) {
                            @unlink($file);
                        }
                    } else {
                        // 模块
                        if (filemtime($file) + SYS_CACHE_MINDEX < SYS_TIME) {
                            @unlink($file);
                        }
                    }
                }
            }
        }

        // 单页
        if (SYS_CACHE_PAGE) {
            $files = directory_map(WEBPATH.'cache/page/', 1);
            if ($files) {
                foreach ($files as $t) {
                    $file = WEBPATH.'cache/page/'.$t;
                    if (filemtime($file) + SYS_CACHE_PAGE < SYS_TIME) {
                        @unlink($file);
                    }
                }
            }
        }

        // 自动更新模块缓存（3小时一次）移动端不执行
        if (!$this->mobile) {
            $file = WEBPATH.'cache/cron/module.cache';
            $auto = is_file($file) ? (int)file_get_contents($file) : 0;
            if (!$auto || $auto + 10800 < SYS_TIME) {
                $this->clear_cache('module');
                @file_put_contents($file, SYS_TIME);
            }
        }

        // 每天更新模块内容的日点击量

        // 未到发送时间
        if (get_cookie('cron')) {
            exit('');
        }

        // 清理一个小时未活动的会员
        $this->db->where('`time` < '.(SYS_TIME - 3600))->delete('member_online');

        // 在线人数清理
        $num = max(100, (int)SYS_ONLINE_NUM);
        $this->db->query('delete from '.$this->db->dbprefix('member_online').' where uid not in (select t.uid from (select uid from '.$this->db->dbprefix('member_online').' order by `time` desc limit '.$num.') as t)');

        // 一次执行的任务数量
        $pernum = defined('SYS_CRON_NUMS') && SYS_CRON_NUMS ? SYS_CRON_NUMS : 10;

        // 用户每多少秒调用本程序
        set_cookie('cron', 1, SYS_CRON_TIME);

        // 查询所有队列记录
        $queue = $this->db->order_by('status ASC,id ASC')->limit($pernum)->get('cron_queue')->result_array();
        if (!$queue) {
            // 所有任务执行完毕
            $this->db->query('TRUNCATE `'.$this->db->dbprefix('cron_queue').'`');
            exit('');
        }

        foreach ($queue as $data) {
            $this->cron_model->execute($data);
        }

        // 本次任务执行完毕
        exit('');
    }

}