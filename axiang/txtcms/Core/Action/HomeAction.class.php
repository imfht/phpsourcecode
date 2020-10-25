<?php
class HomeAction extends TxtcmsAction{
	public function _init(){
		parent::_init();
		if(config('web_close')){
			$this->error(config('web_closecon'));
		}
		import('Class/robot');
		$robot=new robot;
		if($robot->check()){
			$data['is_robot']=true;
		}
		if(config('web_robot_onnotes')) $robot->notes();
		if(APP_DEBUG or config('web_debug')){
			$this->tplConf('compile_check',true);
			$this->tplConf('caching',false);
		}
		//获取广告代码
		$adlist=DB('myad')->select();
		foreach($adlist as $k=>$vo){
			$mark=$adlist[$k]['mark'];
			$data['myad'][$mark]=$adlist[$k]['code'];
		}
		$data['web_path']=__ROOT__;
		$data['theme_path']=__ROOT__.'/Template/'.config('web_default_theme');
		$this->tplConf('template_dir',TMPL_PATH.config('web_default_theme'));
		$this->assign($data);
	}
}