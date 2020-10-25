<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


 
class Search extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 共享内容搜索
     */
    public function index() {


		// 对指定模块搜索
		$this->dir = 'share';

		$mod = $this->get_cache('module-'.SITE_ID.'-'.$this->dir);

		// 加载搜索模型
		if (is_file(FCPATH.'dayrui/models/Search_share_model.php')) {
			require_once FCPATH.'dayrui/models/Search_share_model.php';
		} else {
			require_once FCPATH.'dayrui/models/Search_model.php';
		}

		$this->search_model = new Search_model();

		// 清除过期缓存
		$this->search_model->clear((int)SYS_CACHE_MSEARCH);

		// 搜索参数
		$get = $this->input->get(NULL, TRUE);
		$get = isset($get['rewrite']) ? dr_rewrite_decode($get['rewrite']) : $get;

		$id = $get['id'];
		$catid = (int)$get['catid'];
		$_GET['page'] = $get['page'];
		$get['keyword'] = str_replace(array('%', ' '), array('', '%'), urldecode($get['keyword']));
		unset($get['s'], $get['c'], $get['m'], $get['id'], $get['page']);


		if ($id) {
			// 读缓存数据
			$data = $this->search_model->get($id);
			$catid = $data['catid'];
			$data['get'] = $data['params'];
			if (!$data) {
				$this->msg(fc_lang('搜索缓存已过期，请重新搜索'));
			}
		} else {
			// 实时组合搜索条件
			$data = $this->search_model->set($get);
		}

		list($parent, $related) = $this->_related_cat($mod, $catid);

		$this->template->assign(array(
			'cat' => $mod['category'][$catid],
			'get' => @array_merge($get, $data['params']),
			'caitd' => $catid,
			'parent' => $parent,
			'related' => $related,
			'keyword' => $get['keyword'],
			'urlrule' => dr_share_search_url($data['params'], 'page', '{page}'),
			'sototal' => $data['contentid'] ? substr_count($data['contentid'], ',') + 1 : 0,
			'searchid' => $data['id'],
		));
		$this->template->assign($data);
		$this->template->display($catid && $mod['category'][$catid]['setting']['template']['search'] ? $mod['category'][$catid]['setting']['template']['search'] : 'search.html');
    }

}