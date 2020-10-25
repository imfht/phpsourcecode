<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



define('DR_IS_SO', 1);
require_once FCPATH.'branch/fqb/D_Module.php';

class Html extends D_Module {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('生成静态') => array('admin/html/index', 'file'),
		)));
    }
	
	/**
     * 生成静态
     */
    public function index() {

        $MOD = $this->get_module(SITE_ID);
        $module = array();
        if ($MOD) {
            foreach ($MOD as $t) {
                if (!$t['share'] && !$t['site'][SITE_ID]['html']) {
                    continue; // 非共享模块必须开启生成才显示
                }
                $page = $this->get_cache('page-'.SITE_ID, 'data', $t['dirname']);
                if ($t['share'] && !$page) {
                    continue; // 共享模块不存在单页时不显示
                }
                $t['select'] = $this->select_category_new($t['category'], 0, 'multiple name=\'data[catid][]\' style="min-width:200px;height:250px;"', fc_lang('-- 全部 --'), 'category');
                //$t['page_select'] = $page ? $this->select_category_new($page, 0, 'multiple name=\'data[catid][]\' style="min-width:200px;height:250px;"', fc_lang('-- 全部 --'), 'page') : '';
                $module[$t['dirname']] = $t;
            }
        }

        //$page = $this->get_cache('page-'.SITE_ID, 'data', 'index');
        $share = $this->get_cache('module-'.SITE_ID.'-share', 'category');

        $dir = $this->input->get('dir');
        if ($dir) {
            if ($this->get_cache('module-'.SITE_ID.'-'.$dir, 'share')) {
                $module = array();
            } else {
                $module2 = $module;
                $share = array();
                $module = array();
                if ($module2[$dir]) {
                    $module[$dir] = $module2[$dir];
                } else {
                    $this->admin_msg(fc_lang('此模块未开启静态生成'));
                }
            }

        }

        $this->template->assign(array(
            //'page' => $page ? $this->select_category_new($page, 0, 'multiple name=\'data[catid][]\' style="min-width:200px;height:250px;"', fc_lang('-- 全部 --'), 'page') : '',
            'share' => $share ? $this->select_category_new($share, 0, 'multiple name=\'data[catid][]\' style="min-width:200px;height:250px;"', fc_lang('-- 全部 --'), 'category') : '',
            'module' => $module,
        ));
		$this->template->display('html_index.html');
    }


    /**
     * 栏目选择
     *
     * @param array			$data		栏目数据
     * @param intval/array	$id			被选中的ID，多选是可以是数组
     * @param string		$str		属性
     * @param string		$default	默认选项
     * @return string
     */
    public function select_category_new($data, $id = 0, $str = '', $default = ' -- ', $type = '') {

        $tree = array();
        $string = '<select class=\'form-control\' '.$str.'>';

        if ($default) {
            $string.= "<option value='0'>$default</option>";
        }

        if (is_array($data)) {
            foreach($data as $t) {
                // 外部链接不显示
                $is_link = isset($t['setting']['linkurl']) && $t['setting']['linkurl'] ? 1 : (isset($t['tid']) && $t['tid'] == 2 ? 1 : 0);
                if ($is_link) {
                    continue;
                } elseif ($t['urllink']) {
                    continue;
                }
                unset($t['permission'], $t['setting'], $t['catids'], $t['url']);
                $tree[$t['id']] = $t;
            }
        }

        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $str2 = "<optgroup label='\$spacer \$name'></optgroup>";

        $this->load->library('dtree');
        $this->dtree->init($tree);

        $string.= $this->dtree->get_tree_category(0, $str, $str2);
        $string.= '</select>';

        return $string;
    }

}