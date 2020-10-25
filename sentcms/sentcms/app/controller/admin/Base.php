<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\admin;

use app\model\Addons;
use app\model\AuthGroup;
use app\model\Menu;
use app\model\Model;
use think\facade\View;
use think\facade\Config;

class Base extends \app\controller\Base {

	// 使用内置PHP模板引擎渲染模板输出
	protected $tpl_config = [
		'view_dir_name' => 'view',
		'tpl_replace_string' => [
			'__static__' => '/static',
			'__img__' => '/static/admin/images',
			'__css__' => '/static/admin/css',
			'__js__' => '/static/admin/js',
			'__plugins__' => '/static/plugins',
			'__public__' => '/static/admin',
		],
	];

	protected $middleware = [
		'\app\http\middleware\Validate',
		'\app\http\middleware\Admin',
	];

	protected function initialize() {
		$url = str_replace(".", "/", strtolower($this->request->controller())) . '/' . $this->request->action();
		if (!is_login() and !in_array($url, array('admin/index/login', 'admin/index/logout', 'admin/index/verify'))) {
			$this->redirect('/admin/index/login');
		}

		if (!in_array($url, array('admin/index/login', 'admin/index/logout', 'admin/index/verify'))) {

			// 是否是超级管理员
			define('IS_ROOT', is_administrator());
			if (!IS_ROOT && $this->config['admin_allow_ip']) {
				// 检查IP地址访问
				if (!in_array(get_client_ip(), explode(',', $this->config['admin_allow_ip']))) {
					$this->error('403:禁止访问');
				}
			}

			// 检测系统权限
			if (!IS_ROOT) {
				$access = $this->accessControl();
				if (false === $access) {
					$this->error('403:禁止访问');
				} elseif (null === $access) {
					$dynamic = $this->checkDynamic(); //检测分类栏目有关的各项动态权限
					if ($dynamic === null) {
						//检测访问权限
						if (!$this->checkRule($url, [1,2])) {
							$this->error('未授权访问!');
						} else {
							// 检测分类及内容有关的各项动态权限
							$dynamic = $this->checkDynamic();
							if (false === $dynamic) {
								$this->error('未授权访问!');
							}
						}
					} elseif ($dynamic === false) {
						$this->error('未授权访问!');
					}
				}
			}
			//菜单设置
			$this->getMenu();

			View::assign('meta_title', isset($this->data['meta_title']) ? $this->data['meta_title'] : $this->getCurrentTitle());
		}
	}

	/**
	 * 权限检测
	 * @param string  $rule    检测的规则
	 * @param string  $mode    check模式
	 * @return boolean
	 * @author 朱亚杰  <xcoolcc@gmail.com>
	 */
	final protected function checkRule($rule, $type = AuthRule::rule_url, $mode = 'url') {
		static $Auth = null;
		if (!$Auth) {
			$Auth = new \sent\auth\Auth();
		}
		if (!$Auth->check($rule, session('userInfo.uid'), $type, $mode)) {
			return false;
		}
		return true;
	}

	/**
	 * 检测是否是需要动态判断的权限
	 * @return boolean|null
	 *      返回true则表示当前访问有权限
	 *      返回false则表示当前访问无权限
	 *      返回null，则表示权限不明
	 *
	 * @author 朱亚杰  <xcoolcc@gmail.com>
	 */
	protected function checkDynamic() {
		if (IS_ROOT) {
			return true; //管理员允许访问任何页面
		}
		return null; //不明,需checkRule
	}

	/**
	 * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
	 *
	 * @return boolean|null  返回值必须使用 `===` 进行判断
	 *
	 *   返回 **false**, 不允许任何人访问(超管除外)
	 *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
	 *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
	 * @author 朱亚杰  <xcoolcc@gmail.com>
	 */
	final protected function accessControl() {
		$allow = [];
		$deny = [];
		foreach ($this->config['allow_visit'] as $key => $value) {
			$allow[] = $value['label'];
		}
		foreach ($this->config['deny_visit'] as $key => $value) {
			$deny[] = $value['label'];
		}
		$check = strtolower(str_replace(".", "/", $this->request->controller()) . '/' . $this->request->action());
		if (!empty($deny) && in_array_case($check, $deny)) {
			return false; //非超管禁止访问deny中的方法
		}
		if (!empty($allow) && in_array_case($check, $allow)) {
			return true;
		}
		return null; //需要检测节点权限
	}

	protected function getMenu() {
		$addon = $this->request->param('addon', false);
		$hover_url = str_replace(".", "/", strtolower($this->request->controller()));
		$controller = str_replace(".", "/", strtolower($this->request->controller()));
		$menu = array(
			'main' => array(),
			'child' => array(),
		);
		$where['pid'] = 0;
		$where['hide'] = 0;
		$where['type'] = 'admin';
		if (!config('develop_mode')) {
			// 是否开发者模式
			$where['is_dev'] = 0;
		}
		$row = Menu::where($where)->order('sort asc')->field("id,title,url,icon,'' as style")->select();
		foreach ($row as $key => $value) {
			//此处用来做权限判断
			if (!IS_ROOT && !$this->checkRule(substr($value['url'], 1), 2, null)) {
				unset($menu['main'][$value['id']]);
				continue; //继续循环
			}
			if (false !== strripos($controller, $value['url'])) {
				$value['style'] = "active";
			}
			$menu['main'][$value['id']] = $value;
		}

		// 查找当前子菜单
		$pid = Menu::where("pid !=0 AND url like '%{$hover_url}%'")->value('pid');
		$id = Menu::where("pid = 0 AND url like '%{$hover_url}%'")->value('id');
		$pid = $pid ? $pid : $id;
		if (strtolower($hover_url) == 'admin/content' || strtolower($hover_url) == 'admin/attribute') {
			//内容管理菜单
			$pid = Menu::where("pid =0 AND url like '%admin/category%'")->value('id');
		}
		if ($addon) {
			//扩展管理菜单
			$pid = Menu::where("pid =0 AND url like '%admin/addons%'")->value('id');
			$this->getAddonsMenu();
		}
		if ($pid) {
			$map['pid'] = $pid;
			$map['hide'] = 0;
			$map['type'] = 'admin';
			$row = Menu::field("id,title,url,icon,`group`,pid,'' as style")->where($map)->order('sort asc')->select();
			foreach ($row as $key => $value) {
				if (IS_ROOT || $this->checkRule(substr($value['url'], 1), 2, null)) {
					if ($controller == $value['url']) {
						$menu['main'][$value['pid']]['style'] = "active";
						$value['style'] = "active";
					}
					$menu['child'][$value['group']][] = $value;
				}
			}
		}
		View::assign('__menu__', $menu);
	}

	protected function getContentMenu() {
		$list = [];
		$menu = [];
		$map[] = ['status', '>', 0];
		$list = Model::where($map)->field("name,id,title,icon,'' as 'style'")->select();

		//判断是否有模型权限
		$models = AuthGroup::getAuthModels(session('userInfo.uid'));
		foreach ($list as $key => $value) {
			if (IS_ROOT || in_array($value['id'], $models)) {
				if ('/admin/content/index' == $this->request->url() && input('model_id') == $value['id']) {
					$value['style'] = "active";
				}
				$value['url'] = "/admin/" . $value['name'] . "/index";
				$value['title'] = $value['title'] . "管理";
				$value['icon'] = $value['icon'] ? $value['icon'] : 'file';
				$menu[] = $value;
			}
		}
		if (!empty($menu)) {
			View::assign('extend_menu', array('内容管理' => $menu));
		}
	}

	protected function getAddonsMenu() {
		$list = array();
		$map[] = ['isinstall', '>', 0];
		$map[] = ['status', '>', 0];
		$list = Addons::where($map)->field("name,id,title,'' as 'style'")->select();

		$menu = array();
		foreach ($list as $key => $value) {
			$class = "\\addons\\" . strtolower($value['name']) . "\\controller\\Admin";
			if (is_file($this->app->getRootPath() . '/addons/' . strtolower($value['name']) . "/controller/Admin.php")) {
				$action = get_class_methods($class);
				$value['url'] = "/addons/" . $value['name'] . "/admin/" . $action[0];
				$menu[$key] = $value;
			}
		}
		if (!empty($menu)) {
			View::assign('extend_menu', array('管理插件' => $menu));
		}
	}
}