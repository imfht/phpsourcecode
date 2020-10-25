<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\api;

use app\model\Attach as AttachModel;

/**
 * @title 附件管理
 */
class Attach extends Base {

	/**
	 * @title 附件列表
	 */
	public function index(){
		$param = $this->request->param();
		$map = [];
		$order = "id desc";

		if(isset($param['type']) && $param['type']){
			$map[] = ['type', '=', $param['type']];
		}

		$list = AttachModel::where($map)->order($order)->paginate($this->request->pageConfig);

		$this->data['data'] = $list;
		$this->data['code'] = 1;
		return $this->data;
	}
}