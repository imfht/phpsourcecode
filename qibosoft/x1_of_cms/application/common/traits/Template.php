<?php
namespace app\common\traits;
trait Template{
	/**
	 * 使用方法 $template=$this->get_tpl('模板名');
	 * @param string $type
	 * @return string|string[]
	 */
	protected function get_tpl($type='show'){
		$template='';
		if(empty($template)){
			$template=$this->get_auto_tpl($type);
		}
		if(empty($template)){ //新风格找不到的话,就寻找默认default模板
			if(config('template.view_base')){
				if(config('template.default_view_base')){ //没有使用默认风格
					$view_base=config('template.view_base');
					$style=config('template.index_style');
					config('template.view_base',config('template.default_view_base'));
					config('template.index_style','default');   // check_file 此方法要用到
					$template=$this->get_auto_tpl($type);
					config('template.view_base',$view_base);
					config('template.index_style',$style);
				}
			}else{
				if(config('template.default_view_path')!=''){
					$view_path=config('template.view_path');
					$style=config('template.index_style');
					config('template.view_path',config('template.default_view_path'));
					config('template.index_style','default');
					$template=$this->get_auto_tpl($type);
					config('template.view_path',$view_path);
					config('template.index_style',$style);
				}
			}
		}
		return $template;
	}
	protected function check_file($filename){
		static $path;
		if(empty($path[config('template.index_style')])){
			$path[config('template.index_style')]=dirname(makeTemplate('show',false)).'/';
		}
		$file=$path[config('template.index_style')].$filename.'.'.ltrim(config('template.view_suffix'),'.');
		if(IN_WAP===true){
			if(!empty($this->webdb['wapstyle'])){
				$file=str_replace(config('template.index_style'),$this->webdb['wapstyle'],$file);
			}
		}else{
			if(!empty($this->webdb['style'])){
				$file=str_replace(config('template.index_style'),$this->webdb['style'],$file);
			}
		}
		if(is_file($file)){
			return $file;
		}
	}
	protected function get_auto_tpl($type='show'){
		if(IN_WAP===true){
			$template=$this->check_file('wap_'.$type);
		}else{
			$template=$this->check_file('pc_'.$type);
		}
		if(empty($template)){
			$template=$this->check_file($type);
		}
		if(empty($template)){
			if(IN_WAP===true){
				$template=$this->check_file($type);
			}else{
				$template=$this->check_file('pc_'.$type);
			}
		}
		if(empty($template)){
			$template=$this->check_file($type);
		}
		return $template;
	}
}





