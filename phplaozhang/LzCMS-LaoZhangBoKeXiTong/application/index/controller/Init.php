<?php
namespace app\index\controller;

use think\Controller;

/**
* index模块基础类
*/
class Init extends Controller
{
	
	function _initialize()
	{
		if(!file_exists(APP_PATH.'install.lock')){
			$this->redirect(url('/install/index'));
		}
		parent::_initialize();
		error_reporting(0);
		if(!session('?member')){ session('member.id',0);}
		$this->settings = cache('settings');
		if(!session('?admin_user') && $this->settings['site_status'] != 1){
			exit('<meta charset="UTF-8"><p style="text-align:center;font-size:42px;color:#f00;line-height:60px;font-weight:bold;padding-top:100px;margin-bottom:0">网站暂时关闭...</p><p style="width:500px;margin:0 auto;padding:20px;line-height24px;font-weight:bold;">关闭原因：'.$this->settings['site_closedreason'].'</p>');
		}
		$this->categorys = cache('categorys');
		$this->models = cache('models');
		$search_model_ids = explode(',', $this->settings['search_model']);
		foreach ($search_model_ids as $model_id) {
			$search_model_select[$model_id] = $this->models[$model_id];
		}
		$this->seo['title'] = $this->settings['site_name'].$this->settings['title_add'];
		$this->seo['keywords'] = $this->settings['keywords'];
		$this->seo['description'] = $this->settings['description'];
		// 发送基本信息
		$this->assign(['settings' => $this->settings,'categorys' => $this->categorys,'search_model_select'=>$search_model_select,'seo' => $this->seo]);
	}


}