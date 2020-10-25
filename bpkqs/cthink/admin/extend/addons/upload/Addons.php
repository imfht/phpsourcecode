<?php
namespace addons\upload;
/**
 * 上传图片插件
 */
class Addons extends \app\common\controller\Addons{
	
	public function run($data,$aoname){
		$config = array(
			'count'			=> isset($data['count'])?$data['count']:1,
			'size'			=> isset($data['size'])?$data['size']:'2048KB',
			'width'			=> isset($data['w'])?$data['w']:0,
			'height'		=> isset($data['h'])?$data['h']:0,
			'exts'			=> isset($data['type'])?$data['type']:'*.jpg;*.jpeg;*.png;*.gif',
			'crop'			=> isset($data['crop'])?$data['crop']:0,
			'total'			=> isset($data['total'])?$data['total']:1,
			'attachidlist'  => isset($data['data'])?$data['data']:'',
			'name'			=> isset($data['name'])?$data['name']:'attach_ids',
			'iahave'		=> isset($data['iahave'])?$data['iahave']:'yes',	//是否为必填项
			'isloads'		=> isset($data['isloads'])?$data['isloads']:'yes',	//同一个页面是否加载多个上传钩子，默认yes，如果加载多个上传插件，第一个设置成yes，其他都必须设置为no，否则将会报错
			'randstr'		=> rand_string(6,3),
		);
		$attachidlist = isset($data['data'])?$data['data']:'';
		$edit = $this->getAttachList($attachidlist);
		$config['data'] = $edit;
		$this->view->assign('data',$config);
		return $this->view->fetch($aoname.':index');
	}
	
	/**
	 * 批量查询当前idlist的图片
	 */
	public function getAttachList($attachidlist){
		$return = false;
		if($attachidlist){
			$list = model('Attach')->getInlist($attachidlist);
			$return = $list;
		}
		return $return;
	}
   
}
