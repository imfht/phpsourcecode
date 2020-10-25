<?php
namespace app\duxcms\controller;
use app\admin\controller\AdminController;

/**
 * 栏目管理
 */
class AdminCategoryController extends AdminController {
	/**
	 * 当前模块参数
	 */
	public function _infoModule() {
		$data = array('info' => array('name' => '栏目管理',
			'description' => '管理网站全部栏目',
		),
			'menu' => array(
				array('name' => '栏目列表',
					'url' => url('duxcms/AdminCategory/index'),
					'icon' => 'list',
				),
			),
		);
		$modelList = get_all_service('ContentModel', '');
		$contentMenu = array();
		if (!empty($modelList)) {
			$i = 0;
			foreach ($modelList as $key => $value) {
				$i++;
				$data['add'][$i]['name'] = '添加' . $value['name'] . '栏目';
				$data['add'][$i]['url'] = url($key . '/AdminCategory/add');
				$data['add'][$i]['icon'] = 'plus';
			}
		}
		return $data;
	}
	/**
	 * 列表
	 */
	public function index() {
		$breadCrumb = array('栏目列表' => url());
		$this->assign('breadCrumb', $breadCrumb);
		$this->assign('list', target('Category')->loadList());
		$this->adminDisplay();
	}
}
