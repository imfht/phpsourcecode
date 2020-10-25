<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\user;

use app\model\Form;
use app\model\Model;
use think\facade\Cache;
use think\facade\Config;
use think\facade\View;

class Base extends \app\controller\Base {

	protected $outAuthUrl = ['user/index/login', 'user/index/logout', 'user/index/verify', 'user/index/register', 'user/index/forget', 'user/index/resetpasswd'];

	protected function initialize() {
		$url = str_replace(".", "/", strtolower($this->request->controller())) . '/' . $this->request->action();
		if (!is_login() && !in_array($url, $this->outAuthUrl)) {
			$this->redirect('/user/index/login');
		}

		if (!in_array($url, array('user/index/login', 'user/index/logout', 'user/index/verify'))) {
			$map = [];
			$model = Model::where($map)->column('name, title, icon', 'name');
			View::assign('model', $model);
			$form = Form::where($map)->column('id, name, title', 'name');
			View::assign('form', $form);
			View::assign('meta_title', isset($this->data['meta_title']) ? $this->data['meta_title'] : $this->getCurrentTitle());
		}
	}

	protected function fetch($template = '') {
		$config = Cache::get('system_config_data');
		$this->tpl_config['view_depr'] = '_';
		$pc_themes = $config['pc_themes'] ? $config['pc_themes'] . DIRECTORY_SEPARATOR : "";
		$this->tpl_config['view_dir_name'] = 'public' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $pc_themes;
		if ($this->isMobile() && $config['mobile_themes']) {
			$mobile_themes = $config['mobile_themes'] ? $config['mobile_themes'] . DIRECTORY_SEPARATOR : "";
			$this->tpl_config['view_dir_name'] = 'public' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $mobile_themes;
			if (!file_exists($this->app->getRootPath() . $this->tpl_config['view_dir_name'])) {
				$this->tpl_config['view_dir_name'] = 'public' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $pc_themes;
			}
		}
		if (!file_exists($this->app->getRootPath() . $this->tpl_config['view_dir_name'] . DIRECTORY_SEPARATOR . 'user')) {
			$this->tpl_config['view_dir_name'] = 'public' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'default';
		}
		if ($template == '') {
			$template = str_replace(".", "@", strtolower($this->request->controller())) . "/" . $this->request->action();
		}
		$template_path = str_replace("public", "", $this->tpl_config['view_dir_name']);
		$this->tpl_config['tpl_replace_string'] = [
			'__static__' => '/static',
			'__img__' => $template_path . DIRECTORY_SEPARATOR . 'static/images',
			'__css__' => $template_path . DIRECTORY_SEPARATOR . 'static/css',
			'__js__' => $template_path . DIRECTORY_SEPARATOR . 'static/js',
			'__plugins__' => '/static/plugins',
			'__public__' => $template_path . DIRECTORY_SEPARATOR . 'static',
		];

		View::config($this->tpl_config);
		View::assign($this->data);
		return View::fetch($template);
	}
}