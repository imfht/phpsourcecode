<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



require_once FCPATH.'branch/fqb/D_Module.php';

class So extends D_Module {

    /**
     * 高级版搜索
     */
    public function __construct() {
        define('DR_IS_SO', TRUE);
        parent::__construct();
    }

    /**
     * 搜索跳转
     */
    public function index() {

        $mod = $this->get_module(SITE_ID);
        if ($mod) {
            // 搜索参数
            $get = $this->input->get(NULL, TRUE);
            $get = isset($get['rewrite']) ? dr_rewrite_decode($get['rewrite']) : $get;
            unset($get['s'], $get['c'], $get['m'], $get['id'], $get['page']);
            $dir = $get['module'] == 'MOD_DIR' ? '' : $get['module'];
            $module = array();
            foreach ($mod as $mdir => $t) {
                if (!$t['setting']['search']['close'] && $t['is_system']) {
                    $module[$mdir]['dir'] = $mdir;
                    $module[$mdir]['url'] = $t['url'];
                    $module[$mdir]['name'] = $t['name'];
                    $module[$mdir]['search'] = $this->_search($mdir);
                    if (!$dir
                        && isset($module[$mdir]['search']['data']['params']['keyword'])
                        && $module[$mdir]['search']['sototal']) {
                        $dir = $mdir;
                    }
                }
            }
            $now = $dir && isset($module[$dir]) ? $module[$dir] : reset($module);
            $dir = $now['dir'];
            $now = $now['search'];
            if ($get['keyword'] != '' || $now['data']['keyword'] != '') {
                $this->template->assign(array(
                    'module' => $module,
                    'dirname' => $dir,
                    'keyword' => $now['data']['keyword'] ? $now['data']['keyword'] : $get['keyword']
                ));
                $this->template->assign($now['seoinfo']);
                $this->template->assign($now['data']);
                unset($now['seoinfo'], $now['data'], $now['keyword']);
                $this->template->assign($now);
                $this->template->assign('params', $get);
                $this->template->assign('urlrule', dr_so_url($get, 'page', '[page]'));
                $this->template->display($get['name'] ? $get['name'] : 'solist.html');
            } else {
                $get['name'] && exit('error');
                $this->template->display('so.html');
            }
        } else {
            $this->msg(fc_lang('您还没有安装任何模块呢~'));
        }

    }

}