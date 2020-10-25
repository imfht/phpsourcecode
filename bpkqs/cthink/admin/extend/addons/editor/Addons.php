<?php
namespace addons\editor;
/**
 * 上传图片插件
 */
class Addons extends \app\common\controller\Addons{
	
	public function run($data,$aoname){
		$config = [
			'editor_height' 	=> 300,
			'editor_resize_type'=> 1,
			'name'				=> isset($data['name'])?$data['name']:'editor_name',
			'value'				=> isset($data['value'])?$data['value']:'',
			'translate'			=> isset($data['id'])?$data['id']:'editor_id',
		];
		$this->assign('editor_config',$config);
		return $this->view->fetch($aoname.':index');
	}
   
}
