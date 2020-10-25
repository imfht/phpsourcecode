<?php
namespace Common\Widget;
use Think\Controller;

/**
 * 上传插件widget
 * 用于动态调用分类信息
 */
class FormWidget extends Controller{

	public function show($type = 'text', $field = 'name', $value = null, $size = 12, $option = array()){
		$size = $size ? $size : 12;
		//类型合并
		if (in_array($type, array('string','num'))) {
			$type = 'text';
		}
		if (in_array($type, array('picture'))) {
			$type = 'image';
		}
		$data = array(
			'type'      =>$type,
			'field'    =>$field,
			'value'     =>$value,
			'size'      =>$size,
			'option'    =>$option
		);
		$this->assign($data);
		if (is_file(COMMON_PATH.'View/Default/Form/'.$type.'.html')) {
			$template = COMMON_PATH.'View/Default/Form/'.$type.'.html';
		}else{
			$template = COMMON_PATH.'View/Default/Form/show.html';
		}
		$this->display($template);
	}
}