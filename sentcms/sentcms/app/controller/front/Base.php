<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\front;

use think\facade\View;
use think\facade\Cache;

class Base extends \app\controller\Base {

	protected function fetch($template = '') {
		$config = Cache::get('system_config_data');
		$this->tpl_config['view_depr'] = '_';
		$pc_themes = $config['pc_themes'] ? $config['pc_themes'] . DIRECTORY_SEPARATOR : "";
		$this->tpl_config['view_dir_name'] = 'public' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $pc_themes;
		if ($this->isMobile() && $config['mobile_themes']) {
			$mobile_themes = $config['mobile_themes'] ? $config['mobile_themes'] . DIRECTORY_SEPARATOR : "";
			$this->tpl_config['view_dir_name'] = 'public' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $mobile_themes;
			if (!is_dir($this->app->getRootPath() . $this->tpl_config['view_dir_name'])) {
				$this->tpl_config['view_dir_name'] = 'public' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $pc_themes;
			}
		}
		if ($template == '') {
			$template = str_replace(".", "@", strtolower($this->request->controller())) . "/" . $this->request->action();
		}
		if($this->request->param('addon')){
			$this->tpl_config['view_depr'] = '/';
			$this->tpl_config['view_dir_name'] = 'addons' . DIRECTORY_SEPARATOR . $this->request->param('addon') . DIRECTORY_SEPARATOR . 'view';
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